<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $portfolios = Portfolio::with([
        'craftman.user',
        'craftman.profession'
    ])
    ->orderBy('id','desc')
    ->paginate(4);

    return response()->view('cms.portfolio.index', compact('portfolios'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $craftmen = Craftman::all();
        return response()->view('cms.portfolio.create', compact('craftmen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = validator(
            $request->all(),
            [
                'title' => 'required|string|max:200',
                'description' => 'required|string',
                'portfolioCover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'location' => 'nullable|string|max:255',
                'status' => 'required',
                'craftman_id' => 'required',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',

            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        } else {
            $portfolios = new Portfolio();

            if (request()->hasFile('portfolioCover_image')) {
                $image = $request->file('portfolioCover_image');
                $imageName = time() . '_' . $image->getClientOriginalExtension();
                $image->move('storage/images/portfolio', $imageName);
                $portfolios->portfolioCover_image = $imageName;
            }

            $portfolios->title = $request->input('title');
            $portfolios->description = $request->input('description');
            $portfolios->location = $request->input('location');
            $portfolios->status = $request->input('status');
            $portfolios->craftman_id = $request->input('craftman_id');
            $portfolios->project_date = now();
$portfolios->views_count = 0;
            $isSaved = $portfolios->save();
            return response()->json([
                'icon' => 'success',
                'title' => 'تم الحفظ بنجاح',
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $portfolios = Portfolio::with([
        'craftman.user',
        'craftman.profession'
    ])->findOrFail($id);

    // 🟢 زيادة عداد المشاهدات بمقدار 1
    $portfolios->increment('views_count');

    return view('cms.portfolio.show', compact('portfolios'));
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $portfolios = Portfolio::findOrFail($id);
        $craftmen = Craftman::all();
        return response()->view('cms.portfolio.edit', compact('portfolios', 'craftmen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = validator(
            $request->all(),
            [
                'title' => 'required|string|max:200',
                'description' => 'required|string',
                // 'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'location' => 'nullable|string|max:255',
                'craftman_id' => 'required',
                'status' => 'required',
            ],
            [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',

            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'icon' => 'خطأ في البيانات',
                'title' => $validator->getMessageBag()->first(),
            ], 400);
        }
        $portfolios = Portfolio::find($id);

        if (request()->hasFile('portfolioCover_image')) {
            $image = $request->file('portfolioCover_image');
            $imageName = time() . '_' . $image->getClientOriginalExtension();
            $image->move('storage/images/portfolio', $imageName);
            $portfolios->portfolioCover_image = $imageName;
        }

        if (! $portfolios) {
            return response()->json([
                'icon'  => 'خطاء في البيانات',
                'title' => 'هذا المشروع غير موجود',
            ], 400);
        }

        $isUpdated = $portfolios->update($validator->validated());
        if ($isUpdated) {
            return ['redirect' => route('portfolios.index')];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $portfolios = Portfolio::destroy($id);
    }

    public function trashed()
    {
        $portfolios = Portfolio::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return response()->view('cms.portfolio.trashed', compact('portfolios'));
    }

    public function restore($id)
    {
        $portfolios = Portfolio::onlyTrashed()->findOrFail($id);
        $portfolios->restore();
        return redirect()->route('portfolios_trashed')->with('success', 'تم استعادة المعرض بنجاح.');
    }
    public function forceDestroy($id)
    {
        $portfolios = Portfolio::onlyTrashed()->findOrFail($id);
        $portfolios->forceDelete();
        return redirect()->route('portfolios_trashed')->with('success', 'تم حذف المعرض بشكل دائم.');
    }
    public function DestroyAll()
    {
        Portfolio::onlyTrashed()->forceDelete();
        return redirect()->route('portfolios_trashed')->with('success', 'تم حذف جميع المعارض بشكل دائم.');
    }
}
