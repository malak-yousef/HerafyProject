<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['pending', 'reviewing', 'resolved', 'rejected'])],
            'reporter_id' => ['nullable', Rule::exists('users', 'id')], 'reported_id' => ['nullable', Rule::exists('users', 'id')], 'job_id' => ['nullable', Rule::exists('service_jobs', 'id')->whereNull('deleted_at')],
            'date_from' => ['nullable', 'date'], 'date_to' => ['nullable', 'date', 'after_or_equal:date_from'], 'sort' => ['nullable', Rule::in(['newest', 'oldest'])],
        ]);
        $query = Report::with([
            'reporter',
            'reported',
            'job.order',
        ]);
        if ($search = trim($validated['search'] ?? '')) {
            $query->where(fn ($q) => $q->where('reason', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhereHas('reporter', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHas('reported', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHas('job.order', fn ($o) => $o->where('title', 'like', "%{$search}%")));
        }
        foreach (['status', 'reporter_id', 'reported_id', 'job_id'] as $f) {
            if (isset($validated[$f])) {
                $query->where($f, $validated[$f]);
            }
        }
        if (isset($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        } if (isset($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }
        ($validated['sort'] ?? 'newest') === 'oldest' ? $query->oldest() : $query->latest();
        $reports = $query->paginate(15)->withQueryString();

        return view('cms.report.index', compact('reports'));
    }

    public function create()
    {
        $users = User::all();
        $jobs = ServiceJob::all();

        return view('cms.report.create', compact('users', 'jobs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id|different:reporter_id',
            'job_id' => 'required|exists:service_jobs,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,reviewing,resolved,rejected',
        ]);

        Report::create($validated);

        return redirect()
            ->route('reports.index')
            ->with('success', 'تم إضافة البلاغ بنجاح');
    }

    public function show(Report $report)
    {
        $report->load([
            'reporter',
            'reported',
            'job',
        ]);

        return view('cms.report.show', compact('report'));
    }

    public function edit(Report $report)
    {
        $users = User::all();
        $jobs = ServiceJob::all();

        return view('cms.report.edit', compact(
            'report',
            'users',
            'jobs'
        ));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'reporter_id' => 'required|exists:users,id',
            'reported_id' => 'required|exists:users,id|different:reporter_id',
            'job_id' => 'required|exists:service_jobs,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,reviewing,resolved,rejected',
        ]);

        $report->update($validated);

        return redirect()
            ->route('reports.index')
            ->with('success', 'تم تعديل البلاغ بنجاح');
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()
            ->route('reports.index')
            ->with('success', 'تم نقل البلاغ إلى المحذوفات');
    }

    public function trashed()
    {
        $reports = Report::onlyTrashed()
            ->with([
                'reporter',
                'reported',
                'job',
            ])
            ->latest('deleted_at')
            ->paginate(15)->withQueryString();

        return view('cms.report.trashed', compact('reports'));
    }

    public function restore($id)
    {
        $report = Report::onlyTrashed()->findOrFail($id);

        $report->restore();

        return redirect()
            ->route('reports.trashed')
            ->with('success', 'تم استعادة البلاغ بنجاح');
    }

    public function forceDelete($id)
    {
        $report = Report::onlyTrashed()->findOrFail($id);

        $report->forceDelete();

        return redirect()
            ->route('reports.trashed')
            ->with('success', 'تم حذف البلاغ نهائياً');
    }
}
