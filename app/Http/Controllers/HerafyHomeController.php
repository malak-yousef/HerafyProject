<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HerafyHomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredCraftmen = Craftman::query()
            ->publiclyDiscoverable()
            ->where('is_featured', true)
            ->with(['user', 'profession'])
            ->withAvg([
                'reviews as approved_reviews_avg_rating' => fn (Builder $query) => $query->where('status', 'approved'),
            ], 'rating')
            ->withCount([
                'reviews as approved_reviews_count' => fn (Builder $query) => $query->where('status', 'approved'),
            ])
            ->latest('craftmen.updated_at')
            ->limit(4)
            ->get();

        $featuredPortfolios = Portfolio::query()
            ->where('status', true)
            ->where('is_featured', true)
            ->whereHas('craftman', fn (Builder $query) => $query->publiclyDiscoverable())
            ->with(['craftman.user', 'craftman.profession'])
            ->latest('portfolios.updated_at')
            ->limit(6)
            ->get();

        $approvedReviews = Review::query()->where('status', 'approved');
        $approvedReviewsCount = (clone $approvedReviews)->count();
        $averageRating = round((float) ((clone $approvedReviews)->avg('rating') ?? 0), 1);
        $satisfiedReviewsCount = (clone $approvedReviews)->where('rating', '>=', 4)->count();

        $testimonials = Review::query()
            ->where('status', 'approved')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->whereHas('customer.user', fn (Builder $query) => $query->where('status', 'active'))
            ->with(['customer.user', 'craftsman.user'])
            ->latest()
            ->limit(3)
            ->get();

        $homepageStats = [
            'active_craftmen' => Craftman::query()->publiclyDiscoverable()->count(),
            'completed_jobs' => ServiceJob::query()->where('status', 'completed')->count(),
            'satisfaction_rate' => $approvedReviewsCount > 0
                ? (int) round(($satisfiedReviewsCount / $approvedReviewsCount) * 100)
                : 0,
            'average_rating' => $averageRating,
        ];

        return view('herafy.index', compact('featuredCraftmen', 'featuredPortfolios', 'testimonials', 'homepageStats'));
    }
}
