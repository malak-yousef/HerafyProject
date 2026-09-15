<?php

namespace App\Http\Controllers;

use App\Http\Requests\CmsReviewRequest;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\CustomerReview;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Notifications\ReviewSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function craftmanStoreCustomerReview(Request $request, int $serviceJob): RedirectResponse
    {
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000', 'not_regex:/<[^>]*>/'],
        ]);

        DB::transaction(function () use ($craftman, $serviceJob, $validated): void {
            $job = ServiceJob::query()
                ->where('craftman_id', $craftman->id)
                ->lockForUpdate()
                ->findOrFail($serviceJob);

            abort_unless(
                $job->status === 'completed'
                    && $job->completed_at !== null
                    && $job->customer()->exists(),
                409,
                'This service job is not eligible for customer review.'
            );

            abort_if(
                CustomerReview::withTrashed()->where('service_job_id', $job->id)->lockForUpdate()->exists(),
                409,
                'This customer has already been reviewed for this job.'
            );

            CustomerReview::create([
                'service_job_id' => $job->id,
                'craftman_id' => $job->craftman_id,
                'customer_id' => $job->customer_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);
        });

        return redirect()
            ->route('herafy.craftman.jobs.show', $serviceJob)
            ->with('success', 'تم إرسال تقييم العميل بنجاح.');
    }

    public function customerStore(Request $request, int $serviceJob): RedirectResponse
    {
        $user = auth('customer')->user();
        $customer = $user?->actor;

        abort_unless($user?->actor_type === Customer::class && $customer instanceof Customer, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000', 'not_regex:/<[^>]*>/'],
        ]);

        [$orderId, $review, $job, $reviewedCraftman] = DB::transaction(function () use ($customer, $serviceJob, $validated): array {
            $job = ServiceJob::query()
                ->where('customer_id', $customer->id)
                ->lockForUpdate()
                ->findOrFail($serviceJob);
            $order = Order::query()->lockForUpdate()->find($job->order_id);
            $offer = Offer::query()->lockForUpdate()->find($job->offer_id);

            abort_unless(
                $order
                    && $offer
                    && $job->status === 'completed'
                    && $job->completed_at !== null
                    && $order->status === 'completed'
                    && $offer->status === 'completed'
                    && $job->customer_id === $order->customer_id
                    && $job->craftman_id === $offer->craftman_id
                    && $offer->order_id === $order->id
                    && $offer->craftman()->exists(),
                409,
                'This service job is not eligible for review.'
            );

            abort_if(
                Review::withTrashed()->where('request_id', $order->id)->lockForUpdate()->exists(),
                409,
                'This service job has already been reviewed.'
            );

            $reviewedCraftman = Craftman::query()->with('user')->lockForUpdate()->find($job->craftman_id);
            abort_unless(
                $reviewedCraftman
                    && $reviewedCraftman->user
                    && $reviewedCraftman->user->actor_type === Craftman::class
                    && $reviewedCraftman->user->actor_id === $reviewedCraftman->id,
                409,
                'The reviewed craftman is unavailable.'
            );

            $review = Review::create([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'craftsman_id' => $job->craftman_id,
                'customer_id' => $job->customer_id,
                'request_id' => $order->id,
                'status' => 'approved',
            ]);

            return [$order->id, $review, $job, $reviewedCraftman];
        });

        $job->load('order');
        $reviewedCraftman->user->notify(new ReviewSubmitted($review, $job));

        return redirect()
            ->route('herafy.customer.orders.show', $orderId)
            ->with('success', 'تم إرسال تقييمك بنجاح.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'craftman_id' => ['nullable', Rule::exists('craftmen', 'id')->whereNull('deleted_at')],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'rating_desc', 'rating_asc'])],
        ]);
        $query = Review::with([
            'craftsman.user',
            'customer.user',
            'request',
        ]);
        if ($search = trim($validated['search'] ?? '')) {
            $query->where(fn ($q) => $q->whereHas('request', fn ($o) => $o->where('title', 'like', "%{$search}%"))->orWhereHas('craftsman.user', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHas('customer.user', fn ($u) => $u->where('name', 'like', "%{$search}%")));
        }
        foreach (['status', 'rating', 'craftman_id'] as $field) {
            if (isset($validated[$field])) {
                $query->where($field, $validated[$field]);
            }
        }
        if (isset($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }
        if (isset($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->orderBy('rating', str_ends_with($sort, 'desc') ? 'desc' : 'asc');
        }
        $reviews = $query->paginate(15)->withQueryString();
        $craftmen = Craftman::with('user')->whereNull('deleted_at')->orderBy('id')->get();

        return view('cms.review.index', compact('reviews', 'craftmen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $craftmen = Craftman::with('user')->get();
        $customers = Customer::with('user')->get();
        $requests = Order::with('customer.user')->get();

        return view('cms.review.create', compact(
            'craftmen',
            'customers',
            'requests'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CmsReviewRequest $request)
    {
        Review::create($request->validated());

        return redirect()
            ->route('reviews.index')
            ->with('success', 'تم إضافة التقييم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load([
            'craftsman.user',
            'customer.user',
            'request',
        ]);

        return view('cms.review.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $craftmen = Craftman::with('user')->get();
        $customers = Customer::with('user')->get();
        $requests = Order::with('customer.user')->get();

        return view('cms.review.edit', compact(
            'review',
            'craftmen',
            'customers',
            'requests'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $review->update(['status' => $validated['status']]);

        return redirect()
            ->route('reviews.index')
            ->with('success', $review->status === 'approved' ? 'تم إظهار التقييم بنجاح.' : 'تم إخفاء التقييم بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Review::destroy($id);
    }

    public function trashed()
    {
        $reviews = Review::onlyTrashed()
            ->with(['request', 'customer.user', 'craftsman.user'])
            ->orderByDesc('deleted_at')
            ->paginate(15)->withQueryString();

        return response()->view('cms.review.trashed', compact('reviews'));
    }

    public function restore($id)
    {
        Review::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        Review::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Success');
    }

    public function forceAll(Request $request)
    {
        Review::onlyTrashed()->whereKey($this->trashIds($request))->forceDelete();

        return back()->with('success', 'Success');
    }

    private function trashIds(Request $request): array
    {
        return $request->validate(['ids' => ['required', 'array', 'min:1'], 'ids.*' => ['integer', 'distinct']])['ids'];
    }
}
