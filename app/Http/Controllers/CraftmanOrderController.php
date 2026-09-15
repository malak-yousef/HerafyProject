<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Order;

class CraftmanOrderController extends Controller
{
    /**
     * عرض الطلبات للحرفي
     */
    public function index()
    {
        $craftman = $this->authenticatedCraftman();

        $orders = Order::query()
            ->with(['customer.user', 'city', 'area', 'profession'])
            ->eligibleForCraftman($craftman)
            ->latest()
            ->paginate(10);

        return view('herafy.order.index', compact('orders'));
    }

    /**
     * تفاصيل الطلب للحرفي
     */
    public function show(int $order)
    {
        $craftman = $this->authenticatedCraftman();

        $order = Order::query()
            ->eligibleForCraftman($craftman)
            ->with([
                'customer.user',
                'city',
                'area',
                'profession',
                'images',
                'offers' => fn ($query) => $query->where('craftman_id', $craftman->id),
            ])
            ->findOrFail($order);

        return view('herafy.order.show', compact('order'));
    }

    private function authenticatedCraftman(): Craftman
    {
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

        return $craftman;
    }
}
