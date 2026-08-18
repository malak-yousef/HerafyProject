<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\ServiceJob;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with([
            'reporter',
            'reported',
            'job'
        ])->latest()->paginate(10);

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
            'job'
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
                'job'
            ])
            ->latest('deleted_at')
            ->paginate(10);

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