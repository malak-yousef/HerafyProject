<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\ServiceJob;
use App\Notifications\NewEligibleOrder;
use App\Notifications\OfferAccepted;
use App\Services\ImageOptimizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CustomerOrderController extends Controller
{
    public function index(): View
    {
        $customer = $this->authenticatedCustomer();

        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->with(['profession', 'city', 'area'])
            ->withCount('images')
            ->latest()
            ->paginate(10);

        return view('herafy.customer-order.index', compact('orders'));
    }

    public function show(int $order): View
    {
        $customer = $this->authenticatedCustomer();

        $order = Order::query()
            ->where('customer_id', $customer->id)
            ->with([
                'profession',
                'city',
                'area',
                'images',
                'offers.craftman.user',
                'serviceJob.offer.craftman.user',
                'serviceJob.craftman.user',
                'serviceJob.review',
                'serviceJob.customerReview.craftman.user',
            ])
            ->findOrFail($order);

        return view('herafy.customer-order.show', compact('order'));
    }

    public function cancel(int $order): RedirectResponse
    {
        $customer = $this->authenticatedCustomer();

        DB::transaction(function () use ($customer, $order): void {
            $ownedOrder = Order::query()
                ->where('customer_id', $customer->id)
                ->lockForUpdate()
                ->findOrFail($order);

            abort_unless($ownedOrder->status === 'pending', 409, 'Only pending orders can be cancelled.');

            $ownedOrder->update(['status' => 'cancelled']);
        });

        return redirect()
            ->route('herafy.customer.orders.show', $order)
            ->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    public function acceptOffer(int $order, int $offer): RedirectResponse
    {
        $customer = $this->authenticatedCustomer();

        [$acceptedOffer, $serviceJob] = DB::transaction(function () use ($customer, $order, $offer): array {
            $ownedOrder = Order::query()
                ->where('customer_id', $customer->id)
                ->lockForUpdate()
                ->findOrFail($order);

            abort_unless($ownedOrder->status === 'pending', 409, 'Only pending orders can accept an offer.');

            $offers = Offer::query()
                ->where('order_id', $ownedOrder->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $selectedOffer = $offers->firstWhere('id', $offer);

            abort_unless($selectedOffer, 404);
            abort_unless($selectedOffer->status === 'pending', 409, 'This offer can no longer be accepted.');
            abort_if($offers->contains('status', 'accepted'), 409, 'An offer has already been accepted.');
            $selectedCraftman = $selectedOffer->craftman()->with('user')->first();
            abort_unless(
                $selectedCraftman
                    && $selectedCraftman->user
                    && $selectedCraftman->user->actor_type === Craftman::class
                    && $selectedCraftman->user->actor_id === $selectedCraftman->id,
                409,
                'The selected craftman is unavailable.'
            );
            abort_if(
                ServiceJob::withTrashed()
                    ->where(fn ($query) => $query
                        ->where('order_id', $ownedOrder->id)
                        ->orWhere('offer_id', $selectedOffer->id))
                    ->lockForUpdate()
                    ->exists(),
                409,
                'A service job already exists for this acceptance.'
            );

            $selectedOffer->update(['status' => 'accepted']);
            $ownedOrder->update(['status' => 'accepted']);
            $serviceJob = ServiceJob::create([
                'order_id' => $ownedOrder->id,
                'offer_id' => $selectedOffer->id,
                'customer_id' => $ownedOrder->customer_id,
                'craftman_id' => $selectedOffer->craftman_id,
                'status' => 'pending',
                'started_at' => null,
                'completed_at' => null,
            ]);

            return [$selectedOffer, $serviceJob];
        });

        $acceptedOffer->load(['order', 'craftman.user']);
        $acceptedOffer->craftman->user->notify(new OfferAccepted($acceptedOffer, $serviceJob));

        return redirect()
            ->route('herafy.customer.orders.show', $order)
            ->with('success', 'تم قبول العرض بنجاح.');
    }

    public function create(): View
    {
        return view('herafy.request', [
            'professions' => Profession::query()->where('status', 'active')->orderBy('name')->get(),
            'cities' => City::query()
                ->with(['areas' => fn ($query) => $query->orderBy('name')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = $this->authenticatedCustomer();

        $validated = $request->validate([
            'profession_id' => ['required', 'integer', Rule::exists('professions', 'id')->where('status', 'active')->whereNull('deleted_at')],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')->whereNull('deleted_at')],
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->where(fn ($query) => $query
                    ->where('city_id', $request->integer('city_id'))
                    ->whereNull('deleted_at')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'service_date' => ['nullable', 'date', 'after_or_equal:today'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $customer, &$storedPaths): void {
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'profession_id' => $validated['profession_id'],
                    'city_id' => $validated['city_id'],
                    'area_id' => $validated['area_id'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'budget' => $validated['budget'] ?? null,
                    'service_date' => $validated['service_date'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'status' => 'pending',
                ]);

                foreach ($request->file('images', []) as $image) {
                    $path = app(ImageOptimizerService::class)->forMedia($image)->store("order-images/{$order->id}", 'public');
                    $storedPaths[] = $path;
                    $order->images()->create(['image_path' => $path]);
                }

                DB::afterCommit(function () use ($order): void {
                    $order->load('profession');
                    Craftman::query()
                        ->eligibleForOrderNotification($order)
                        ->with('user')
                        ->get()
                        ->pluck('user')
                        ->unique('id')
                        ->each(fn ($user) => $user->notify(new NewEligibleOrder($order)));
                });
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        return redirect()->route('request')->with('success', 'تم إرسال طلبك بنجاح.');
    }

    private function authenticatedCustomer(): Customer
    {
        $user = auth('customer')->user();
        $customer = $user?->actor;

        abort_unless($user?->actor_type === Customer::class && $customer instanceof Customer, 403);

        return $customer;
    }
}
