<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Offer;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Setting;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $offers = offer::with(['order', 'craftman.user'])->paginate(10);

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
public function craftmanStore(Request $request, Order $order)
{
    $request->validate([
        'price' => 'required|numeric|min:1',
        'message' => 'required|string',
    ]);

    // المستخدم الحالي
    $user = auth('craftman')->user();

    // الحرفي المرتبط بالمستخدم
    $craftman = $user->actor;

    if (!$craftman) {
        return back()
            ->withInput()
            ->with('error', 'لم يتم العثور على بيانات الحرفي.');
    }

    // 🔴 منع الحرفي من تقديم أكثر من عرض على نفس الطلب
    $alreadyOffered = Offer::where('order_id', $order->id)
        ->where('craftman_id', $craftman->id)
        ->exists();

    if ($alreadyOffered) {
        return redirect()
            ->route('herafy.order.show', $order->id)
            ->with('error', 'لقد قدمت عرضًا على هذا الطلب مسبقًا.');
    }

    // الحد الأقصى للعروض اليومية
    $maxDailyOffers = (int) Setting::where('key', 'max_daily_offers')
        ->value('value');

    if ($maxDailyOffers <= 0) {
        $maxDailyOffers = 10;
    }

    // عدد عروض الحرفي اليوم
    $todayOffers = Offer::where('craftman_id', $craftman->id)
        ->whereDate('created_at', today())
        ->count();

    if ($todayOffers >= $maxDailyOffers) {
        return back()
            ->withInput()
            ->with(
                'error',
                "لقد وصلت إلى الحد الأقصى للعروض اليومية وهو {$maxDailyOffers} عروض."
            );
    }

    // ✅ إنشاء العرض بعد التأكد أنه لم يقدم سابقًا
    Offer::create([
        'order_id' => $order->id,
        'craftman_id' => $craftman->id,
        'price' => $request->price,
        'message' => $request->message,
        'status' => 'pending',
    ]);

    return redirect()
        ->route('herafy.order.show', $order->id)
        ->with('success', 'تم تقديم العرض بنجاح.');
}

public function craftmanOffers()
{
    $craftman = auth('craftman')->user()->actor;

    $offers = offer::where('craftman_id', $craftman->id)
        ->with('order')
        ->latest()
        ->paginate(10);

    return view('herafy.offer.index', compact('offers'));
}
public function craftmanCreate(Order $order)
{
    return view('herafy.offer.create', compact('order'));
}
public function craftmanShow(Offer $offer)
{
    $offer->load([
        'order',
        'craftman.user'
    ]);

    return view('herafy.offer.show', compact('offer'));
}
}
