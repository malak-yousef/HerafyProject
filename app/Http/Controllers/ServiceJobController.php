<?php

namespace App\Http\Controllers;

use App\Http\Requests\CmsServiceJobRequest;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ServiceJob;
use App\Notifications\ServiceJobCompleted;
use App\Notifications\ServiceJobStarted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceJobController extends Controller
{
    public function craftmanIndex()
    {
        $craftman = $this->authenticatedCraftman();

        $jobs = ServiceJob::query()
            ->where('craftman_id', $craftman->id)
            ->with(['order', 'offer'])
            ->latest()
            ->paginate(10);

        return view('herafy.service-job.index', compact('jobs'));
    }

    public function craftmanShow(int $serviceJob)
    {
        $craftman = $this->authenticatedCraftman();

        $serviceJob = ServiceJob::query()
            ->where('craftman_id', $craftman->id)
            ->with([
                'order.customer.user',
                'order.profession',
                'order.city',
                'order.area',
                'offer',
                'review',
                'customerReview',
            ])
            ->findOrFail($serviceJob);

        return view('herafy.service-job.show', compact('serviceJob'));
    }

    public function craftmanStart(int $serviceJob): RedirectResponse
    {
        $craftman = $this->authenticatedCraftman();

        [$job, $customer] = DB::transaction(function () use ($craftman, $serviceJob): array {
            [$job, $order, $offer, $customer] = $this->lockedLifecycleRecords($serviceJob, $craftman);

            abort_unless(
                $job->status === 'pending'
                    && $job->started_at === null
                    && $job->completed_at === null
                    && $order->status === 'accepted'
                    && $offer->status === 'accepted',
                409,
                'This service job cannot be started from its current state.'
            );

            $startedAt = now();
            $offer->update(['status' => 'in_progress']);
            $order->update(['status' => 'in_progress']);
            $job->update([
                'status' => 'in_progress',
                'started_at' => $startedAt,
                'completed_at' => null,
            ]);

            return [$job, $customer];
        });

        $job->load(['order', 'craftman.user']);
        $customer->user->notify(new ServiceJobStarted($job));

        return redirect()
            ->route('herafy.craftman.jobs.show', $serviceJob)
            ->with('success', 'تم بدء العمل بنجاح.');
    }

    public function craftmanComplete(int $serviceJob): RedirectResponse
    {
        $craftman = $this->authenticatedCraftman();

        [$job, $customer] = DB::transaction(function () use ($craftman, $serviceJob): array {
            [$job, $order, $offer, $customer] = $this->lockedLifecycleRecords($serviceJob, $craftman);

            abort_unless(
                $job->status === 'in_progress'
                    && $job->started_at !== null
                    && $job->completed_at === null
                    && $order->status === 'in_progress'
                    && $offer->status === 'in_progress',
                409,
                'This service job cannot be completed from its current state.'
            );

            $offer->update(['status' => 'completed']);
            $order->update(['status' => 'completed']);
            $job->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return [$job, $customer];
        });

        $job->load(['order', 'craftman.user']);
        $customer->user->notify(new ServiceJobCompleted($job));

        return redirect()
            ->route('herafy.craftman.jobs.show', $serviceJob)
            ->with('success', 'تم إكمال العمل بنجاح.');
    }

    /**
     * Display a listing of the service jobs.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->whereNull('deleted_at')], 'craftman_id' => ['nullable', Rule::exists('craftmen', 'id')->whereNull('deleted_at')], 'order_id' => ['nullable', Rule::exists('orders', 'id')->whereNull('deleted_at')],
            'started_from' => ['nullable', 'date'], 'started_to' => ['nullable', 'date', 'after_or_equal:started_from'], 'completed_from' => ['nullable', 'date'], 'completed_to' => ['nullable', 'date', 'after_or_equal:completed_from'], 'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'started_desc', 'started_asc', 'completed_desc', 'completed_asc'])],
        ]);
        $query = ServiceJob::with([
            'order',
            'order.customer.user', 'offer', 'customer.user', 'craftman.user',
        ]);
        if ($search = trim($validated['search'] ?? '')) {
            $query->where(fn ($q) => $q->whereHas('order', fn ($o) => $o->where('title', 'like', "%{$search}%"))->orWhereHas('customer.user', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHas('craftman.user', fn ($u) => $u->where('name', 'like', "%{$search}%")));
        }
        foreach (['status', 'customer_id', 'craftman_id', 'order_id'] as $f) {
            if (isset($validated[$f])) {
                $query->where($f, $validated[$f]);
            }
        }
        foreach ([['date_from', 'date_to', 'created_at'], ['started_from', 'started_to', 'started_at'], ['completed_from', 'completed_to', 'completed_at']] as [$from,$to,$column]) {
            if (isset($validated[$from])) {
                $query->whereDate($column, '>=', $validated[$from]);
            } if (isset($validated[$to])) {
                $query->whereDate($column, '<=', $validated[$to]);
            }
        }
        $sort = $validated['sort'] ?? 'newest';
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->orderBy(str_starts_with($sort, 'started') ? 'started_at' : 'completed_at', str_ends_with($sort, 'desc') ? 'desc' : 'asc')->orderBy('id', 'desc');
        }
        $jobs = $query->paginate(15)->withQueryString();
        $customers = Customer::with('user')->whereNull('deleted_at')->get();
        $craftsmen = Craftman::with('user')->whereNull('deleted_at')->get();
        $orders = Order::whereNull('deleted_at')->latest()->get(['id', 'title']);

        return view('cms.serviceJob.index', compact('jobs', 'customers', 'craftsmen', 'orders'));
    }

    /**
     * Show the form for creating a new service job.
     */
    public function create()
    {
        $orders = Order::latest()->get();
        $offers = Offer::latest()->get();
        $customers = Customer::latest()->get();
        $craftsmen = Craftman::latest()->get();

        return view('cms.serviceJob.create', compact(
            'orders',
            'offers',
            'customers',
            'craftsmen'
        ));
    }

    /**
     * Store a newly created service job.
     */
    public function store(CmsServiceJobRequest $request)
    {
        ServiceJob::create($request->validated());

        return redirect()
            ->route('service-jobs.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح.');
    }

    /**
     * Display the specified service job.
     */
    public function show(ServiceJob $serviceJob)
    {
        $serviceJob->load([
            'order',
            'offer',
            'customer',
            'craftman',
        ]);

        return view('cms.serviceJob.show', compact('serviceJob'));
    }

    /**
     * Show the form for editing the specified service job.
     */
    public function edit(ServiceJob $serviceJob)
    {
        $orders = Order::latest()->get();
        $offers = Offer::latest()->get();
        $customers = Customer::latest()->get();
        $craftsmen = Craftman::latest()->get();

        return view('cms.serviceJob.edit', compact(
            'serviceJob',
            'orders',
            'offers',
            'customers',
            'craftsmen'
        ));
    }

    /**
     * Update the specified service job.
     */
    public function update(CmsServiceJobRequest $request, ServiceJob $serviceJob)
    {
        $serviceJob->update($request->validated());

        return redirect()
            ->route('service-jobs.index')
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    /**
     * Soft delete the specified service job.
     */
    public function destroy(ServiceJob $serviceJob)
    {
        $serviceJob->delete();

        return redirect()
            ->route('service-jobs.index')
            ->with('success', 'تم نقل الخدمة إلى سلة المحذوفات.');
    }

    /**
     * Display trashed service jobs.
     */
    public function trashed()
    {
        $jobs = ServiceJob::onlyTrashed()->with(['order', 'customer.user', 'craftman.user'])->orderByDesc('deleted_at')->paginate(15)->withQueryString();

        return view('cms.serviceJob.trashed', compact('jobs'));
    }

    /**
     * Restore a deleted service job.
     */
    public function restore($id)
    {
        $serviceJob = ServiceJob::onlyTrashed()->findOrFail($id);

        $serviceJob->restore();

        return redirect()
            ->route('service-jobs.trashed')
            ->with('success', 'تم استعادة الخدمة بنجاح.');
    }

    /**
     * Permanently delete a service job.
     */
    public function forceDelete($id)
    {
        $serviceJob = ServiceJob::onlyTrashed()->findOrFail($id);

        $serviceJob->forceDelete();

        return redirect()
            ->route('service-jobs.trashed')
            ->with('success', 'تم حذف الخدمة نهائيًا.');
    }

    private function authenticatedCraftman(): Craftman
    {
        $user = auth('craftman')->user();
        $craftman = $user?->actor;

        abort_unless($user?->actor_type === Craftman::class && $craftman instanceof Craftman, 403);

        return $craftman;
    }

    private function lockedLifecycleRecords(int $serviceJob, Craftman $craftman): array
    {
        $job = ServiceJob::query()
            ->where('craftman_id', $craftman->id)
            ->lockForUpdate()
            ->findOrFail($serviceJob);
        $order = Order::query()->lockForUpdate()->find($job->order_id);
        $offer = Offer::query()->lockForUpdate()->find($job->offer_id);
        $customer = Customer::query()->with('user')->lockForUpdate()->find($job->customer_id);

        abort_unless(
            $order
                && $offer
                && $customer
                && $customer->user
                && $customer->user->actor_type === Customer::class
                && $customer->user->actor_id === $customer->id
                && $job->customer_id === $order->customer_id
                && $job->craftman_id === $offer->craftman_id
                && $offer->order_id === $order->id,
            409,
            'The service job relationships are inconsistent.'
        );

        return [$job, $order, $offer, $customer];
    }
}
