<?php

namespace App\Http\Controllers;

use App\Http\Requests\CmsOfferRequest;
use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Setting;
use App\Notifications\NewOfferSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])],
            'craftman_id' => ['nullable', Rule::exists('craftmen', 'id')->whereNull('deleted_at')], 'order_id' => ['nullable', Rule::exists('orders', 'id')->whereNull('deleted_at')],
            'price_min' => ['nullable', 'numeric', 'min:0'], 'price_max' => ['nullable', 'numeric', 'min:0', 'gte:price_min'],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'price_asc', 'price_desc'])],
        ]);
        $query = Offer::with(['order', 'craftman.user']);
        if ($search = trim($validated['search'] ?? '')) {
            $query->where(fn ($q) => $q->whereHas('order', fn ($o) => $o->where('title', 'like', "%{$search}%"))->orWhereHas('craftman.user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('user_name', 'like', "%{$search}%")));
        }
        foreach (['status', 'craftman_id', 'order_id'] as $field) {
            if (isset($validated[$field])) {
                $query->where($field, $validated[$field]);
            }
        }
        if (isset($validated['price_min'])) {
            $query->where('price', '>=', $validated['price_min']);
        } if (isset($validated['price_max'])) {
            $query->where('price', '<=', $validated['price_max']);
        }
        if (isset($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        } if (isset($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->orderBy('price', $sort === 'price_desc' ? 'desc' : 'asc');
        }
        $offers = $query->paginate(15)->withQueryString();
        $craftmen = Craftman::with('user')->whereNull('deleted_at')->orderBy('id')->get();
        $orders = Order::whereNull('deleted_at')->latest()->get(['id', 'title']);

        return view('cms.offer.index', compact('offers', 'craftmen', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $orders = Order::all();
        $craftmen = Craftman::with('user')->get();
        $selectedOrderId = $orders->firstWhere('id', $request->integer('order_id'))?->id;

        return view('cms.offer.create', compact('orders', 'craftmen', 'selectedOrderId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CmsOfferRequest $request)
    {
        Offer::create($request->validated());

        return redirect()->route('offers.index')
            ->with('success', 'تم إضافة العرض بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        $offer->load([
            'order.customer.user',
            'craftman.user',
        ]);

        return view('cms.offer.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offer $offer)
    {
        $orders = Order::all();
        $craftmen = Craftman::with('user')->get();

        return view('cms.offer.edit', compact(
            'offer',
            'orders',
            'craftmen'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CmsOfferRequest $request, Offer $offer)
    {
        $offer->update($request->validated());

        return redirect()->route('offers.index')
            ->with('success', 'تم تحديث العرض بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->json([
            'icon' => 'success',
            'title' => 'تم نقل العرض إلى سلة المحذوفات',
        ]);
    }

    public function trashed()
    {
        $offers = Offer::onlyTrashed()
            ->with(['order', 'craftman.user'])
            ->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return view('cms.offer.trashed', compact('offers'));
    }

    public function restore($id)
    {
        $offer = Offer::onlyTrashed()->findOrFail($id);

        $offer->restore();

        return redirect()->route('offers_trashed');
    }

    public function force($id)
    {
        $offer = Offer::onlyTrashed()->findOrFail($id);

        $offer->forceDelete();

        return redirect()->route('offers_trashed');
    }

    public function forceAll(Request $request)
    {
        Offer::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return redirect()->route('offers_trashed');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }

    public function craftmanStore(Request $request, Order $order)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
            'message' => 'required|string',
        ]);

        $craftman = $this->authenticatedCraftman();

        // الحد الأقصى للعروض اليومية
        $maxDailyOffers = max((int) Setting::where('key', 'max_daily_offers')->value('value'), 1);

        // إنشاء العرض داخل معاملة مع قفل لمنع العروض المكررة
        [$offer, $blockedReason] = DB::transaction(function () use ($request, $order, $craftman, $maxDailyOffers): array {
            $order = Order::query()
                ->lockForUpdate()
                ->eligibleForCraftman($craftman)
                ->findOrFail($order->id);

            // منع الحرفي من تقديم أكثر من عرض على نفس الطلب
            $alreadyOffered = Offer::where('order_id', $order->id)
                ->where('craftman_id', $craftman->id)
                ->exists();

            if ($alreadyOffered) {
                return [null, 'duplicate'];
            }

            // الحد الأقصى للعروض اليومية
            $todayOffers = Offer::where('craftman_id', $craftman->id)
                ->whereDate('created_at', today())
                ->count();

            if ($todayOffers >= $maxDailyOffers) {
                return [null, 'daily_limit'];
            }

            return [
                Offer::create([
                    'order_id' => $order->id,
                    'craftman_id' => $craftman->id,
                    'price' => $request->price,
                    'message' => $request->message,
                    'status' => 'pending',
                ]),
                null,
            ];
        });

        if ($blockedReason === 'daily_limit') {
            return back()
                ->withInput()
                ->with('error', "لقد وصلت إلى الحد الأقصى للعروض اليومية وهو {$maxDailyOffers} عروض.");
        }

        if (! $offer) {
            return redirect()
                ->route('herafy.order.show', $order->id)
                ->with('error', 'لقد قدمت عرضًا على هذا الطلب مسبقًا.');
        }

        $offer->load(['order.customer.user', 'craftman.user']);
        $offer->order->customer->user->notify(new NewOfferSubmitted($offer));

        return redirect()
            ->route('herafy.order.show', $order->id)
            ->with('success', 'تم تقديم العرض بنجاح.');
    }

    public function craftmanOffers()
    {
        $craftman = $this->authenticatedCraftman();

        $offers = Offer::where('craftman_id', $craftman->id)
            ->with('order')
            ->latest()
            ->paginate(10);

        return view('herafy.offer.index', compact('offers'));
    }

    public function craftmanCreate(Order $order)
    {
        $order = Order::query()
            ->eligibleForCraftman($this->authenticatedCraftman())
            ->findOrFail($order->id);

        return view('herafy.offer.create', compact('order'));
    }

    public function craftmanShow(Offer $offer)
    {
        abort_unless(
            $offer->craftman_id === $this->authenticatedCraftman()->id,
            403
        );

        $offer->load([
            'order',
            'craftman.user',
        ]);

        return view('herafy.offer.show', compact('offer'));
    }

    private function authenticatedCraftman(): Craftman
    {
        $craftman = auth('craftman')->user()?->actor;

        abort_unless($craftman instanceof Craftman, 403);

        return $craftman;
    }
}
