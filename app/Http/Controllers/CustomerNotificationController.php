<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerNotificationController extends Controller
{
    public function index(): View
    {
        return view('herafy.notifications.index', [
            'notifications' => $this->authenticatedUser()->notifications()->latest()->paginate(10),
            'readRoute' => 'herafy.customer.notifications.read',
            'readAllRoute' => 'herafy.customer.notifications.read-all',
        ]);
    }

    public function read(string $notification): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $notification = $user->notifications()->findOrFail($notification);
        $notification->markAsRead();

        if (in_array(($notification->data['event'] ?? null), [
            'new_offer_submitted',
            'service_job_started',
            'service_job_completed',
        ], true)) {
            $orderId = (int) ($notification->data['order_id'] ?? 0);

            if ($user->actor->orders()->whereKey($orderId)->exists()) {
                return redirect()->route('herafy.customer.orders.show', $orderId);
            }
        }

        return redirect()->route('herafy.customer.notifications.index');
    }

    public function readAll(): RedirectResponse
    {
        $this->authenticatedUser()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    private function authenticatedUser(): User
    {
        $user = auth('customer')->user();

        abort_unless($user?->actor_type === Customer::class && $user->actor instanceof Customer, 403);

        return $user;
    }
}
