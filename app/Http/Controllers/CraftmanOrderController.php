<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class CraftmanOrderController extends Controller
{
    /**
     * عرض الطلبات للحرفي
     */
    public function index()
    {
        $orders = Order::with([
            'customer.user',
            'city',
            'area'
        ])
        ->where('status', 'pending')
        ->latest()
        ->paginate(10);

        return view('herafy.order.index', compact('orders'));
    }

    /**
     * تفاصيل الطلب للحرفي
     */
public function show(Order $order)
{
    $order->load([
        'customer.user',
        'city',
        'area',
        'offers.craftman.user',
    ]);

    return view('herafy.order.show', compact('order'));
}
}