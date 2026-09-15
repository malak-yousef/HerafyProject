@extends('cms.parent')

@section('title', 'البلاغات')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>البلاغات</h2>

        <div>
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                إضافة بلاغ
            </a>

            <a href="{{ route('reports.trashed') }}" class="btn btn-secondary">
                المحذوفات
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">

                <form method="GET" class="row g-2 mb-3"><div class="col-md-4"><input name="search" value="{{ request('search') }}" class="form-control" placeholder="بحث"></div><div class="col-md-3"><select name="status" class="form-select"><option value="">كل الحالات</option>@foreach(['pending','reviewing','resolved','rejected'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select></div><div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div><div class="col-md-2"><button class="btn btn-primary" type="submit">بحث</button> <a href="{{ route('reports.index') }}">إعادة ضبط</a></div></form><table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المبلّغ</th>
                            <th>المبلّغ عليه</th>
                            <th>Job</th>
                            <th>السبب</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($reports as $report)

                        <tr>

                            <td>{{ $report->id }}</td>

                            <td>
                                {{ $report->reporter->name ?? 'غير معروف' }}
                            </td>

                            <td>
                                {{ $report->reported->name ?? 'غير معروف' }}
                            </td>

                            <td>
                                #{{ $report->job_id }}
                            </td>

                            <td>
                                {{ $report->reason }}
                            </td>

                            <td>

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

                            </td>

                            <td>
                                {{ $report->created_at->format('Y-m-d') }}
                            </td>

                            <td>

                                <a href="{{ route('reports.show', $report->id) }}"
                                   class="btn btn-sm btn-info">
                                    عرض
                                </a>

                                <a href="{{ route('reports.edit', $report->id) }}"
                                   class="btn btn-sm btn-warning">
                                    تعديل
                                </a>

                                <form action="{{ route('reports.destroy', $report->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('هل أنت متأكد؟')">
                                        حذف
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center">
                                لا توجد بلاغات
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            {{ $reports->links() }}

        </div>
    </div>

</div>

@endsection
