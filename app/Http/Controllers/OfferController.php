<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\offer;
use App\Models\Order;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $offers = Offer::with(['order', 'craftman.user'])->paginate(10);

    return view('cms.offer.index', compact('offers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      

    $orders = Order::all();
    $craftmen = Craftman::with('user')->get();

    return view('cms.offer.create', compact('orders', 'craftmen'));
}
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
        'order_id' => 'required|exists:orders,id',
        'craftman_id' => 'required|exists:craftmen,id',
        'price' => 'required|numeric|min:1',
        'message' => 'required|string',
        'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',
    ]);

    offer::create($request->all());

    return redirect()->route('offers.index')
        ->with('success', 'تم إضافة العرض بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(offer $offer)
    {
         $offer->load([
        'order.customer.user',
        'craftman.user'
    ]);

    return view('cms.offer.show', compact('offer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(offer $offer)
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
   public function update(Request $request, Offer $offer)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'craftman_id' => 'required|exists:craftmen,id',
        'price' => 'required|numeric|min:1',
        'message' => 'required|string',
        'status' => 'required|in:pending,accepted,in_progress,completed,cancelled',
    ]);

    $offer->update($request->all());

    return redirect()->route('offers.index')
        ->with('success', 'تم تحديث العرض بنجاح');
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(offer $offer)
    {
         $offer->delete();

    return response()->json([
        'icon' => 'success',
        'title' => 'تم نقل العرض إلى سلة المحذوفات'
    ]);
    }



   public function trashed()
{
    $offers = offer::onlyTrashed()
        ->with(['order', 'craftman.user'])
        ->get();

    return view('cms.offer.trashed', compact('offers'));
}

public function restore($id)
{
    $offer = offer::onlyTrashed()->findOrFail($id);

    $offer->restore();

    return redirect()->route('offers_trashed');
}

public function force($id)
{
    $offer = offer::onlyTrashed()->findOrFail($id);

    $offer->forceDelete();

    return redirect()->route('offers_trashed');
}

public function forceAll()
{
    offer::onlyTrashed()->forceDelete();

    return redirect()->route('offers_trashed');
}
}
