<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Services\ImageOptimizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CustomerProfileController extends Controller
{
    public function home(): View
    {
        if (! auth('customer')->check()) {
            return view('herafy.main', ['customer' => null]);
        }

        $customer = $this->authenticatedCustomer();
        $orders = Order::query()->where('customer_id', $customer->id);

        return view('herafy.main', [
            'customer' => $customer,
            'recentOrders' => (clone $orders)
                ->with(['profession', 'city'])
                ->latest()
                ->limit(5)
                ->get(),
            'orderSummary' => [
                'pending' => (clone $orders)->where('status', 'pending')->count(),
                'active' => (clone $orders)->whereIn('status', ['accepted', 'in_progress'])->count(),
                'completed' => (clone $orders)->where('status', 'completed')->count(),
            ],
        ]);
    }

    public function show(): View
    {
        $customer = $this->authenticatedCustomer();
        $receivedReviews = $customer->receivedReviews()
            ->with('craftman.user')
            ->latest()
            ->limit(6)
            ->get();

        return view('herafy.customer-profile.show', [
            'customer' => $customer,
            'receivedReviews' => $receivedReviews,
            'receivedReviewsCount' => $customer->receivedReviews()->count(),
            'receivedReviewsAverage' => (float) ($customer->receivedReviews()->avg('rating') ?? 0),
        ]);
    }

    public function edit(): View
    {
        return view('herafy.customer-profile.edit', [
            'customer' => $this->authenticatedCustomer(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = $this->authenticatedCustomer();
        $user = $customer->user;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:45', Rule::unique('users', 'user_name')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $newPath = null;
        $oldPath = $user->profilePhotoPath();

        try {
            if ($request->hasFile('profile_photo')) {
                $newPath = app(ImageOptimizerService::class)->forProfile($request->file('profile_photo'))
                    ->store("profile-photos/customers/{$customer->id}", 'public');
            }

            DB::transaction(function () use ($user, $validated, $newPath): void {
                $user->update([
                    'name' => $validated['name'],
                    'user_name' => $validated['user_name'],
                    'email' => $validated['email'],
                    'mobile' => $validated['mobile'],
                    'address' => $validated['address'],
                    'gender' => $validated['gender'],
                    ...($newPath ? ['image' => $newPath] : []),
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            Log::error('Customer profile update failed.', [
                'customer_id' => $customer->id,
                'exception' => $exception,
            ]);

            return back()->withInput()->with('error', 'تعذر تحديث الملف الشخصي. يرجى المحاولة مرة أخرى.');
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('herafy.customer.profile.show')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    private function authenticatedCustomer(): Customer
    {
        $user = auth('customer')->user();
        $customer = $user?->actor;

        abort_unless($user?->actor_type === Customer::class && $customer instanceof Customer, 403);

        return $customer;
    }
}
