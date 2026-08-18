<?php

namespace App\Http\Controllers;

use App\Models\Craftman;
use App\Models\ServiceJob;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Customer;

use Illuminate\Http\Request;

class ServiceJobController extends Controller
{
    /**
     * Display a listing of the service jobs.
     */
    public function index()
    {
        $jobs = ServiceJob::with([
            'order',
            'offer',
            'customer',
            'craftman'
        ])
        ->latest()
        ->paginate(10);

        return view('cms.serviceJob.index', compact('jobs'));
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'offer_id' => 'required|exists:offers,id',
            'customer_id' => 'required|exists:customers,id',
            'craftman_id' => 'required|exists:craftmen,id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        ServiceJob::create($validated);

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
            'craftman'
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
    public function update(Request $request, ServiceJob $serviceJob)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'offer_id' => 'required|exists:offers,id',
            'customer_id' => 'required|exists:customers,id',
            'craftman_id' => 'required|exists:craftmen,id',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'started_at' => 'nullable|date',
            'completed_at' => 'nullable|date|after_or_equal:started_at',
        ]);

        $serviceJob->update($validated);

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
    $jobs = ServiceJob::onlyTrashed()->get();

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
}