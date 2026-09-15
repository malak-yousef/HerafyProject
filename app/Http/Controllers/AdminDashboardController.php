<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $request->user('admin');

        $metrics = [
            'active_customers' => Customer::query()
                ->whereHas('user', fn (Builder $query) => $query->where('status', 'active'))
                ->count(),
            'active_craftmen' => Craftman::query()
                ->whereHas('user', fn (Builder $query) => $query->where('status', 'active'))
                ->count(),
            'pending_orders' => Order::query()->where('status', 'pending')->count(),
            'pending_offers' => Offer::query()->where('status', 'pending')->count(),
            'active_service_jobs' => ServiceJob::query()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'pending_reviews' => Review::query()->where('status', 'pending')->count(),
            'pending_verifications' => Verification::query()->where('status', 'pending')->count(),
            'active_portfolios' => Portfolio::query()->where('status', true)->count(),
        ];

        $statusSummaries = [
            'orders' => $this->statusCounts(Order::query(), ['pending', 'accepted', 'in_progress', 'completed', 'cancelled']),
            'service_jobs' => $this->statusCounts(ServiceJob::query(), ['pending', 'in_progress', 'completed', 'cancelled']),
            'reviews' => $this->statusCounts(Review::query(), ['pending', 'approved', 'rejected']),
            'verifications' => $this->statusCounts(Verification::query(), ['pending', 'approved', 'rejected']),
        ];

        $pendingVerifications = $admin->can('verifications.view')
            ? Verification::query()
                ->with('craftman.user')
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $pendingReviews = $admin->can('reviews.view')
            ? Review::query()
                ->with(['craftsman.user', 'request'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $recentOrders = Order::query()
            ->with(['customer.user', 'profession'])
            ->latest()
            ->limit(5)
            ->get();

        $recentServiceJobs = ServiceJob::query()
            ->with(['order', 'customer.user', 'craftman.user'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('cms.dashboard', compact(
            'metrics',
            'statusSummaries',
            'pendingVerifications',
            'pendingReviews',
            'recentOrders',
            'recentServiceJobs'
        ));
    }

    /**
     * @param  array<int, string>  $statuses
     * @return array<string, int>
     */
    private function statusCounts(Builder $query, array $statuses): array
    {
        $counts = $query
            ->whereIn('status', $statuses)
            ->select('status')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return collect($statuses)
            ->mapWithKeys(fn (string $status) => [$status => (int) ($counts[$status] ?? 0)])
            ->all();
    }
}
