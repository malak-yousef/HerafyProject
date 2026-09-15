<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CraftmanNotificationController extends Controller
{
    public function index(): View
    {
        return view('herafy.notifications.index', [
            'notifications' => $this->authenticatedUser()->notifications()->latest()->paginate(10),
            'readRoute' => 'herafy.craftman.notifications.read',
            'readAllRoute' => 'herafy.craftman.notifications.read-all',
        ]);
    }

    public function read(string $notification): RedirectResponse
    {
        $user = $this->authenticatedUser();
        $notification = $user->notifications()->findOrFail($notification);
        $notification->markAsRead();

        if (in_array(($notification->data['event'] ?? null), ['offer_accepted', 'review_submitted'], true)) {
            $serviceJobId = (int) ($notification->data['service_job_id'] ?? 0);

            if ($user->actor->serviceJobs()->whereKey($serviceJobId)->exists()) {
                return redirect()->route('herafy.craftman.jobs.show', $serviceJobId);
            }
        }

        if (($notification->data['event'] ?? null) === 'new_eligible_order') {
            $orderId = (int) ($notification->data['order_id'] ?? 0);

            if (Order::query()->eligibleForCraftman($user->actor)->whereKey($orderId)->exists()) {
                return redirect()->route('herafy.order.show', $orderId);
            }
        }

        if (($notification->data['event'] ?? null) === 'verification_status_changed') {
            $verificationId = (int) ($notification->data['verification_id'] ?? 0);

            if ($user->actor->verifications()->whereKey($verificationId)->exists()) {
                return redirect()->route('herafy.verification.show');
            }
        }

        return redirect()->route('herafy.craftman.notifications.index');
    }

    public function readAll(): RedirectResponse
    {
        $this->authenticatedUser()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    private function authenticatedUser(): User
    {
        $user = auth('craftman')->user();

        abort_unless($user?->actor_type === Craftman::class && $user->actor instanceof Craftman, 403);

        return $user;
    }
}
