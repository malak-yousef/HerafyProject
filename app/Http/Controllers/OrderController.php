<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\City;
use App\Models\Area;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with([
            'customer.user',
            'city',
            'area'

        ])
            ->latest()
            ->paginate(10);

        return view('cms.order.index', compact('orders'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //  $users = Customer::with('admin')->get();
      $customers = Customer::with('user')->get();
        $cities = City::all();
        $areas = Area::all();

        return view('cms.order.create', compact(
            'customers',
            'cities',
            'areas'
        ));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data = $request->validate([

            'customer_id' => 'required|exists:customers,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',

            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',

            'service_date' => 'nullable|date',

            'budget' => 'nullable|numeric',

            'status' => 'nullable|in:pending,accepted,in_progress,completed,cancelled',
        ]);


        Order::create($data);


        return redirect()
            ->route('orders.index')
            ->with('success', 'تم إضافة الطلب بنجاح');
    }



    /**
     * Display the specified resource.
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


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {

        $customers = Customer::with('user')->get();
        $cities = City::all();
        $areas = Area::all();


        return view('cms.order.edit', compact(
            'order',
            'customers',
            'cities',
            'areas'
        ));
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {

        $data = $request->validate([

            'customer_id' => 'required|exists:customers,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',

            'title' => 'required|string|max:255',
            'description' => 'required|string',

            'address' => 'required|string',

            'service_date' => 'nullable|date',

            'budget' => 'nullable|numeric',

            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',

        ]);



        $order->update($data);



        return redirect()
            ->route('orders.index')
            ->with('success', 'تم تعديل الطلب بنجاح');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $orders = Order::destroy($id);
    }
    public function trashed()
    {
        $orders = Order::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->view('cms.order.trashed', compact('orders'));
    }
    public function restore($id)
    {
        $orders = Order::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        $orders = Order::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        $orders = Order::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }

    public function craftmanOrders()
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
public function craftmanShow($id)
{
    $order = Order::with([
        'customer.user',
        'city',
        'area',
        'offers'
    ])->findOrFail($id);

    return view('herafy.order.show', compact('order'));
}
}
