<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Services\ImageOptimizerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CraftmanPortfolioController extends Controller
{
    public function index(): View
    {
        $craftman = $this->authenticatedCraftman();
        $portfolios = Portfolio::query()
            ->where('craftman_id', $craftman->id)
            ->withCount('portfolio_images')
            ->latest()
            ->paginate(12);

        return view('herafy.portfolio.index', compact('craftman', 'portfolios'));
    }

    public function create(): View
    {
        return view('herafy.portfolio.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $craftman = $this->authenticatedCraftman();
        $validated = $this->validated($request, true);
        $cover = $this->storeCover($request->file('portfolioCover_image'));

        try {
            DB::transaction(function () use ($craftman, $validated, $cover): void {
                Portfolio::create([
                    ...$validated,
                    'craftman_id' => $craftman->id,
                    'portfolioCover_image' => $cover,
                    'status' => true,
                    'views_count' => 0,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deleteCover($cover);
            throw $exception;
        }

        return redirect()->route('herafy.craftman.portfolios.index')
            ->with('success', 'تمت إضافة العمل إلى معرضك بنجاح.');
    }

    public function edit(int $portfolio): View
    {
        return view('herafy.portfolio.edit', [
            'portfolio' => $this->ownedPortfolio($portfolio),
        ]);
    }

    public function update(Request $request, int $portfolio): RedirectResponse
    {
        $portfolio = $this->ownedPortfolio($portfolio);
        $validated = $this->validated($request, false);
        $oldCover = $portfolio->portfolioCover_image;
        $newCover = $request->hasFile('portfolioCover_image')
            ? $this->storeCover($request->file('portfolioCover_image'))
            : null;

        if ($newCover) {
            $validated['portfolioCover_image'] = $newCover;
        }

        try {
            DB::transaction(fn () => $portfolio->updateOrFail($validated));
        } catch (Throwable $exception) {
            if ($newCover) {
                $this->deleteCover($newCover);
            }
            throw $exception;
        }

        if ($newCover) {
            $this->deleteCover($oldCover);
        }

        return redirect()->route('herafy.craftman.portfolios.index')
            ->with('success', 'تم تحديث العمل بنجاح.');
    }

    public function destroy(int $portfolio): RedirectResponse
    {
        $this->ownedPortfolio($portfolio)->delete();

        return redirect()->route('herafy.craftman.portfolios.index')
            ->with('success', 'تم حذف العمل من معرضك.');
    }

    private function validated(Request $request, bool $coverRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_date' => ['nullable', 'date', 'before_or_equal:today'],
            'portfolioCover_image' => [
                $coverRequired ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:4096',
            ],
        ], [
            'required' => 'هذا الحقل مطلوب.',
            'string' => 'يجب أن يكون هذا الحقل نصًا.',
            'max' => 'تجاوز الملف أو النص الحد الأقصى المسموح به.',
            'image' => 'يجب اختيار صورة صحيحة.',
            'mimes' => 'الصورة يجب أن تكون بصيغة JPG أو PNG أو WEBP.',
            'before_or_equal' => 'تاريخ المشروع لا يمكن أن يكون في المستقبل.',
        ]);
    }

    private function ownedPortfolio(int $id): Portfolio
    {
        return Portfolio::query()
            ->where('craftman_id', $this->authenticatedCraftman()->id)
            ->findOrFail($id);
    }

    private function authenticatedCraftman(): Craftman
    {
        $user = auth('craftman')->user();
        $craftman = $user?->actor;
        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

        return $craftman;
    }

    private function storeCover(UploadedFile $cover): string
    {
        $cover = app(ImageOptimizerService::class)->forMedia($cover);
        $directory = config('filesystems.portfolio_cover_path');
        File::ensureDirectoryExists($directory);
        $filename = now()->format('YmdHis').'_'.Str::random(20).'.'.$cover->extension();
        $cover->move($directory, $filename);

        return $filename;
    }

    private function deleteCover(?string $filename): void
    {
        if (! $filename || basename($filename) !== $filename || str_contains($filename, ['/', '\\'])) {
            return;
        }
        File::delete(config('filesystems.portfolio_cover_path').DIRECTORY_SEPARATOR.$filename);
    }
}
