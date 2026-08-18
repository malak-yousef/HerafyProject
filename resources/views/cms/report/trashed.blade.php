@extends('cms.parent')

@section('title', 'البلاغات المحذوفة')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>البلاغات المحذوفة</h2>

        <a href="{{ route('reports.index') }}"
           class="btn btn-secondary">
            البلاغات
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>المبلّغ</th>
                            <th>المبلّغ عليه</th>
                            <th>السبب</th>
                            <th>تاريخ الحذف</th>
                            <th>الإجراءات</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($reports as $report)

                        <tr>

                            <td>
                                {{ $report->id }}
                            </td>

                            <td>
                                {{ $report->reporter->name ?? 'غير معروف' }}
                            </td>

                            <td>
                                {{ $report->reported->name ?? 'غير معروف' }}
                            </td>

                            <td>
                                {{ $report->reason }}
                            </td>

                            <td>
                                {{ $report->deleted_at->format('Y-m-d H:i') }}
                            </td>

                            <td>

                                <form action="{{ route('reports.restore', $report->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button class="btn btn-sm btn-success">
                                        استعادة
                                    </button>

                                </form>


                                <form action="{{ route('reports.forceDelete', $report->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('سيتم حذف البلاغ نهائياً، هل أنت متأكد؟')">

                                        حذف نهائي

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">
                                لا توجد بلاغات محذوفة
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