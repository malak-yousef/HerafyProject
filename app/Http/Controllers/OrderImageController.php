<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderImage;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // تم إضافة علاقة order للتأكد من إمكانية عرض تفاصيل الطلب في جدول الصور
        $order_images = OrderImage::with('order')->orderBy('id', 'desc')->paginate(10);

        // استبدلي order_images باسم المجلد الحقيقي لديك في views إذا كان مختلفاً
        return response()->view('cms.orderImage.index', compact('order_images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::all();

        return response()->view('cms.orderImage.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [

                'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'order_id' => 'required|exists:orders,id',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',
                'exists' => 'المعرض المحدد غير موجود.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'titel' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $order_images = new OrderImage;

            if (request()->hasFile('image_path')) {
                $image = app(ImageOptimizerService::class)->forMedia($request->file('image_path'));
                $imageName = time().'_'.$image->getClientOriginalName();
                $image->move(public_path('storage/images/order_images'), $imageName);
                $order_images->image_path = $imageName;
            }

            $order_images->order_id = $request->input('order_id');

            $isSaved = $order_images->save();

            return response()->json([
                'icon' => 'success',
                'title' => 'تم إضافة صورة المعرض بنجاح',
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order_images = OrderImage::findOrFail($id);
        $orders = Order::all();

        return response()->view('cms.orderImage.show', compact('order_images', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order_images = OrderImage::findOrFail($id);
        $orders = Order::all();

        return response()->view('cms.orderImage.edit', compact('order_images', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator(
            $request->all(),
            [

                'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'order_id' => 'required|exists:orders,id',
            ],
            [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',
                'exists' => 'المعرض المحدد غير موجود.',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        }

        $order_images = OrderImage::findOrFail($id);
        $oldImage = $order_images->image_path;

        if ($request->hasFile('image_path')) {
            $image = app(ImageOptimizerService::class)->forMedia($request->file('image_path'));
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('storage/images/order_images'), $imageName);
            $order_images->image_path = $imageName;
        }

        $order_images->order_id = $request->input('order_id');

        $order_images->save();

        if ($oldImage && $request->hasFile('image_path')) {
            Storage::disk('public')->delete('images/order_images/'.$oldImage);
        }

        return response()->json([
            'icon' => 'success',
            'title' => 'تم تحديث صورة المعرض بنجاح',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order_images = OrderImage::destroy($id);
    }

    public function trashed()
    {
        $order_images = OrderImage::onlyTrashed()->with('order')->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.orderImage.trashed', compact('order_images'));
    }

    public function restore($id)
    {
        $order_images = OrderImage::onlyTrashed()->findOrFail($id);
        $order_images->restore();

        return redirect()->route('order_images_trashed')->with('success', 'تم استعادة المعرض بنجاح.');
    }

    public function forceDestroy($id)
    {
        $order_images = OrderImage::onlyTrashed()->findOrFail($id);
        $order_images->forceDelete();

        return redirect()->route('order_images_trashed')->with('success', 'تم حذف المعرض بشكل دائم.');
    }

    public function DestroyAll(Request $request)
    {
        OrderImage::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return redirect()->route('order_images_trashed')->with('success', 'تم حذف جميع المعارض بشكل دائم.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
