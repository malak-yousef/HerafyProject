<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\Setting;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolio_images = PortfolioImage::orderBy('id', 'desc')->paginate(4);

        return response()->view('cms.portfolioImage.index', compact('portfolio_images'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $portfolios = Portfolio::all();

        return response()->view('cms.portfolioImage.create', compact('portfolios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /*
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'image_name' => 'required|string|max:200',
                'image_description' => 'nullable|string',
                'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'portfolio_id' => 'required|exists:portfolios,id',
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
            $portfolio_images = new PortfolioImage();

            if (request()->hasFile('image_path')) {
                $image = app(ImageOptimizerService::class)->forMedia($request->file('image_path'));
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move('storage/images/portfolio_images', $imageName);
                $portfolio_images->image_path =  $imageName;
            }

            $portfolio_images->image_name = $request->input('image_name');
            $portfolio_images->image_description = $request->input('image_description');
            $portfolio_images->portfolio_id = $request->input('portfolio_id');

            $isSaved = $portfolio_images->save();

            return response()->json([
                'icon' => 'success',
                'title' => 'تم إضافة صورة المعرض بنجاح',
            ], 200);
        }
    }
*/
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'image_name' => 'required|string|max:200',
                'image_description' => 'nullable|string',
                'image_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'portfolio_id' => 'required|exists:portfolios,id',
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
                'icon' => 'error',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        }

        // جلب الحد الأقصى للصور من جدول settings
        $maxImages = (int) (Setting::where('key', 'max_project_images')
            ->value('value') ?? 5);

        // عدد الصور الموجودة حاليًا داخل هذا المعرض
        $currentImages = PortfolioImage::where(
            'portfolio_id',
            $request->portfolio_id
        )->count();

        // التحقق من الحد
        if ($currentImages >= $maxImages) {
            return response()->json([
                'icon' => 'error',
                'title' => "لا يمكن إضافة المزيد من الصور. الحد الأقصى هو {$maxImages} صور لهذا المعرض.",
            ], 400);
        }

        $portfolio_images = new PortfolioImage;

        if ($request->hasFile('image_path')) {
            $image = app(ImageOptimizerService::class)->forMedia($request->file('image_path'));
            $imageName = time().'_'.$image->getClientOriginalName();

            $image->move(
                public_path('storage/images/portfolio_images'),
                $imageName
            );

            $portfolio_images->image_path = $imageName;
        }

        $portfolio_images->image_name = $request->input('image_name');
        $portfolio_images->image_description = $request->input('image_description');
        $portfolio_images->portfolio_id = $request->input('portfolio_id');

        $portfolio_images->save();

        return response()->json([
            'icon' => 'success',
            'title' => 'تم إضافة صورة المعرض بنجاح',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $portfolio_images = PortfolioImage::findOrFail($id);
        $portfolios = Portfolio::all();

        return response()->view('cms.portfolioImage.show', compact('portfolio_images', 'portfolios'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $portfolio_images = PortfolioImage::findOrFail($id);
        $portfolios = Portfolio::all();

        return response()->view('cms.portfolioImage.edit', compact('portfolio_images', 'portfolios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator(
            $request->all(),
            [
                'image_name' => 'required|string|max:200',
                'image_description' => 'nullable|string',
                'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'portfolio_id' => 'required|exists:portfolios,id',
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

        $portfolio_images = PortfolioImage::findOrFail($id);
        $oldImage = $portfolio_images->image_path;

        if ($request->hasFile('image_path')) {
            $image = app(ImageOptimizerService::class)->forMedia($request->file('image_path'));
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('storage/images/portfolio_images'), $imageName);
            $portfolio_images->image_path = $imageName;
        }

        $portfolio_images->image_name = $request->input('image_name');
        $portfolio_images->image_description = $request->input('image_description');
        $portfolio_images->portfolio_id = $request->input('portfolio_id');

        $portfolio_images->save();

        if ($oldImage && $request->hasFile('image_path')) {
            Storage::disk('public')->delete('images/portfolio_images/'.$oldImage);
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
        $portfolio_images = PortfolioImage::destroy($id);
    }

    public function trashed()
    {
        $portfolio_images = PortfolioImage::onlyTrashed()->with('portfolio')->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return response()->view('cms.portfolioImage.trashed', compact('portfolio_images'));
    }

    public function restore($id)
    {
        $portfolio_images = PortfolioImage::onlyTrashed()->findOrFail($id);
        $portfolio_images->restore();

        return redirect()->route('portfolio_images_trashed')->with('success', 'تم استعادة المعرض بنجاح.');
    }

    public function forceDestroy($id)
    {
        $portfolio_images = PortfolioImage::onlyTrashed()->findOrFail($id);
        $portfolio_images->forceDelete();

        return redirect()->route('portfolio_images_trashed')->with('success', 'تم حذف المعرض بشكل دائم.');
    }

    public function DestroyAll(Request $request)
    {
        PortfolioImage::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return redirect()->route('portfolio_images_trashed')->with('success', 'تم حذف جميع المعارض بشكل دائم.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
