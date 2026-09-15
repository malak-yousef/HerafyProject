<?php

namespace App\Http\Controllers;

use App\Http\Requests\CraftmanDiscoveryRequest;
use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class CraftmanDiscoveryController extends Controller
{
    public function index(CraftmanDiscoveryRequest $request): View
    {
        $filters = $request->validated();
        $search = $filters['search'] ?? null;
        $sort = $filters['sort'] ?? 'newest';

        $craftmen = Craftman::query()
            ->publiclyDiscoverable()
            ->with(['user', 'profession'])
            ->withAvg([
                'reviews as approved_reviews_avg_rating' => fn (Builder $query) => $query->where('status', 'approved'),
            ], 'rating')
            ->withCount([
                'reviews as approved_reviews_count' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->when($search, fn (Builder $query) => $query->whereHas(
                'user',
                fn (Builder $query) => $query->where(
                    fn (Builder $query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('user_name', 'like', "%{$search}%")
                )
            ))
            ->when(
                $filters['profession_id'] ?? null,
                fn (Builder $query, int $professionId) => $query->where('profession_id', $professionId)
            );

        $this->applySort($craftmen, $sort);

        return view('herafy.craftsmen', [
            'craftmen' => $craftmen->paginate(12)->withQueryString(),
            'professions' => Profession::query()->where('status', 'active')->orderBy('name')->get(),
            'filters' => array_merge(['search' => null, 'profession_id' => null, 'sort' => 'newest'], $filters),
        ]);
    }

    public function show(int $craftman): View
    {
        $craftman = Craftman::query()
            ->publiclyDiscoverable()
            ->with(['user', 'profession'])
            ->withAvg([
                'reviews as approved_reviews_avg_rating' => fn (Builder $query) => $query->where('status', 'approved'),
            ], 'rating')
            ->withCount([
                'reviews as approved_reviews_count' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->findOrFail($craftman);

        $portfolios = $craftman->portfolios()
            ->where('status', true)
            ->latest('project_date')
            ->latest()
            ->paginate(9);

        $reviews = $craftman->reviews()
            ->where('status', 'approved')
            ->latest()
            ->limit(6)
            ->get();

        return view('herafy.craftman-public-profile', compact('craftman', 'portfolios', 'reviews'));
    }

    public function portfolio(int $craftman, int $portfolio): View
    {
        $craftman = Craftman::query()
            ->publiclyDiscoverable()
            ->with(['user', 'profession'])
            ->findOrFail($craftman);

        $portfolio = Portfolio::query()
            ->where('craftman_id', $craftman->id)
            ->where('status', true)
            ->with(['portfolio_images' => fn ($query) => $query->latest()])
            ->findOrFail($portfolio);

        $portfolio->increment('views_count');

        return view('herafy.portfolio.show', compact('craftman', 'portfolio'));
    }

    private function applySort(Builder $query, string $sort): void
    {
        if (in_array($sort, ['name_asc', 'name_desc'], true)) {
            $query->orderBy(
                User::query()
                    ->select('name')
                    ->whereColumn('users.actor_id', 'craftmen.id')
                    ->where('users.actor_type', Craftman::class)
                    ->where('users.status', 'active')
                    ->whereNull('users.deleted_at')
                    ->limit(1),
                $sort === 'name_asc' ? 'asc' : 'desc'
            )->orderBy('craftmen.id');

            return;
        }

        match ($sort) {
            'experience_desc' => $query->orderByDesc('experience_years')->orderByDesc('id'),
            'experience_asc' => $query->orderBy('experience_years')->orderBy('id'),
            default => $query->latest(),
        };
    }
}
