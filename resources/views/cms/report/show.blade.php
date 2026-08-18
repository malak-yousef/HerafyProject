@extends('cms.parent')

@section('title', 'تفاصيل البلاغ')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>تفاصيل البلاغ #{{ $report->id }}</h2>

        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>رقم البلاغ:</strong>
                    <p>{{ $report->id }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>تاريخ البلاغ:</strong>
                    <p>{{ $report->created_at->format('Y-m-d H:i') }}</p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>المبلّغ:</strong>
                    <p>
                        {{ $report->reporter->name ?? 'غير معروف' }}
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>المبلّغ عليه:</strong>
                    <p>
                        {{ $report->reported->name ?? 'غير معروف' }}
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Job المرتبط:</strong>
                    <p>
                        #{{ $report->job_id }}
                    </p>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>السبب:</strong>
                    <p>
                        {{ $report->reason }}
                    </p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>الوصف:</strong>

                    <div class="border rounded p-3 mt-2">
                        {{ $report->description ?? 'لا يوجد وصف' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>الحالة:</strong>

                    <p>
                        @if($report->status === 'pending')
                            <span class="badge bg-warning">
                                قيد الانتظار
                            </span>

                        @elseif($report->status === 'reviewing')
                            <span class="badge bg-info">
                                قيد المراجعة
                            </span>

                        @elseif($report->status === 'resolved')
                            <span class="badge bg-success">
                                تم الحل
                            </span>

                        @else
                            <span class="badge bg-danger">
                                مرفوض
                            </span>
                        @endif
                    </p>
                </div>

            </div>

            <div class="mt-3">

                <a href="{{ route('reports.edit', $report->id) }}"
                   class="btn btn-warning">
                    تعديل
                </a>

            </div>

        </div>
    </div>

</div>

@endsection