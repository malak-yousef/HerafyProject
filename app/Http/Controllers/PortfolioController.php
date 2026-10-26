<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolios = Portfolio::with([
            'craftman.user',
            'craftman.profession',
        ])
            ->latest()
            ->paginate(15)->withQueryString();

        return response()->view('cms.portfolio.index', compact('portfolios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $craftmen = Craftman::with('user')->get();

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
                'portfolioCover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'location' => 'nullable|string|max:255',
                'status' => 'required',
                'craftman_id' => 'required',
                'is_featured' => 'nullable|boolean',
            ],
            messages: [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',

            ]
        );
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'خطأ في البيانات', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        }

        if ($request->boolean('is_featured') && Portfolio::where('is_featured', true)->count() >= 6) {
            return back()->withErrors(['is_featured' => 'يمكن اختيار 6 أعمال كحد أقصى ضمن معرض الأعمال في الصفحة الرئيسية.'])->withInput();
        }

        $portfolios = new Portfolio;
        $imageName = $this->storeCover($request->file('portfolioCover_image'));

        try {
            DB::transaction(function () use ($portfolios, $request, $imageName): void {
                $portfolios->portfolioCover_image = $imageName;
                $portfolios->title = $request->input('title');
                $portfolios->description = $request->input('description');
                $portfolios->location = $request->input('location');
                $portfolios->status = $request->input('status');
                $portfolios->craftman_id = $request->input('craftman_id');
                $portfolios->project_date = now();
                $portfolios->views_count = 0;
                $portfolios->is_featured = $request->boolean('is_featured');
                $portfolios->saveOrFail();
            });
        } catch (Throwable $exception) {
            $this->deleteCover($imageName);

            throw $exception;
        }

        return $request->expectsJson() ? response()->json(['icon' => 'success', 'title' => 'تم الحفظ بنجاح']) : redirect()->route('portfolios.index')->with('success', 'تم الحفظ بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $portfolios = Portfolio::with([
            'craftman.user',
            'craftman.profession',
        ])->findOrFail($id);

        return view('cms.portfolio.show', compact('portfolios'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $portfolios = Portfolio::findOrFail($id);
        $craftmen = Craftman::with('user')->get();

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
                'portfolioCover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'location' => 'nullable|string|max:255',
                'craftman_id' => 'required',
                'status' => 'required',
                'is_featured' => 'nullable|boolean',
            ],
            [
                'required' => 'هذا الحقل مطلوب.',
                'string' => 'يجب أن يكون هذا الحقل نصًا.',
                'max' => 'تجاوز الحد الأقصى للطول المسموح به.',

            ]
        );
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['icon' => 'خطأ في البيانات', 'title' => $validator->getMessageBag()->first()], 400);
            }

            return back()->withErrors($validator)->withInput();
        }
        $portfolios = Portfolio::find($id);
        $validated = $validator->validated();

        if (! $portfolios) {
            return response()->json([
                'icon' => 'خطاء في البيانات',
                'title' => 'هذا المشروع غير موجود',
            ], 400);
        }

        if ($request->boolean('is_featured') && ! $portfolios->is_featured
            && Portfolio::where('is_featured', true)->where('id', '!=', $portfolios->id)->count() >= 6) {
            return back()->withErrors(['is_featured' => 'يمكن اختيار 6 أعمال كحد أقصى ضمن معرض الأعمال في الصفحة الرئيسية.'])->withInput();
        }

        $oldCover = $portfolios->portfolioCover_image;
        $newCover = null;

        if ($request->hasFile('portfolioCover_image')) {
            $newCover = $this->storeCover($request->file('portfolioCover_image'));
            $validated['portfolioCover_image'] = $newCover;
        } else {
            unset($validated['portfolioCover_image']);
        }

        try {
            $isUpdated = DB::transaction(fn (): bool => $portfolios->updateOrFail($validated));
        } catch (Throwable $exception) {
            if ($newCover !== null) {
                $this->deleteCover($newCover);
            }

            throw $exception;
        }

        if ($isUpdated) {
            if ($newCover !== null) {
                $this->deleteCover($oldCover);
            }

            return $request->expectsJson() ? ['redirect' => route('portfolios.index')] : redirect()->route('portfolios.index')->with('success', 'تم تحديث المعرض بنجاح.');
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
        $portfolios = Portfolio::onlyTrashed()->with(['craftman.user', 'craftman.profession'])->orderByDesc('deleted_at')->paginate(15)->withQueryString();

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
        $cover = $portfolios->portfolioCover_image;
        $deleted = DB::transaction(fn (): ?bool => $portfolios->forceDelete());

        if ($deleted) {
            $this->deleteCover($cover);
        }

        return redirect()->route('portfolios_trashed')->with('success', 'تم حذف المعرض بشكل دائم.');
    }

    public function DestroyAll(Request $request)
    {
        Portfolio::onlyTrashed()->whereKey($this->trashIds($request))->each(function (Portfolio $portfolio): void {
            $cover = $portfolio->portfolioCover_image;
            $deleted = DB::transaction(fn (): ?bool => $portfolio->forceDelete());

            if ($deleted) {
                $this->deleteCover($cover);
            }
        });

        return redirect()->route('portfolios_trashed')->with('success', 'تم حذف جميع المعارض بشكل دائم.');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }

    private function storeCover(UploadedFile $image): string
    {
        $image = app(ImageOptimizerService::class)->forMedia($image);
        $extension = strtolower($image->getClientOriginalExtension());
        $filename = now()->format('YmdHis').'_'.Str::random(20).'.'.$extension;
        File::ensureDirectoryExists($this->coverDirectory());

        if (! File::copy($image->getRealPath(), $this->coverDirectory().DIRECTORY_SEPARATOR.$filename)) {
            throw new RuntimeException('The Portfolio cover could not be stored.');
        }

        return $filename;
    }

    private function deleteCover(?string $filename): void
    {
        if (! $this->isSafeCoverFilename($filename)) {
            return;
        }

        File::delete($this->coverDirectory().DIRECTORY_SEPARATOR.$filename);
    }

    private function coverDirectory(): string
    {
        return config('filesystems.portfolio_cover_path');
    }

    private function isSafeCoverFilename(?string $filename): bool
    {
        if ($filename === null || $filename === '') {
            return false;
        }

        $normalized = str_replace('\\', '/', $filename);

        return $normalized === basename($normalized);
    }
}
