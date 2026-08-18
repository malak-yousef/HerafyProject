@extends('cms.parent')

@section('title', 'الخدمات المحذوفة')

@section('main_content', 'الخدمات المحذوفة')

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between">

            <h5>
                <i class="fas fa-trash"></i>
                الخدمات المحذوفة
            </h5>

            <a href="{{ route('service-jobs.index') }}"
               class="btn btn-secondary">

                العودة للخدمات

            </a>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover text-center">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>الطلب</th>
                            <th>العميل</th>
                            <th>الحرفي</th>
                            <th>الحالة</th>
                            <th>تاريخ الحذف</th>
                            <th>الإجراءات</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($jobs as $job)

                        <tr>

                            <td>{{ $job->id }}</td>

                            <td>
                                #{{ $job->order_id }}
                            </td>

                            <td>
                                {{ $job->customer->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $job->craftsman->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $job->status }}
                            </td>

                            <td>
                                {{ $job->deleted_at?->format('Y-m-d H:i') }}
                            </td>

                            <td>

                                <form action="{{ route('service-jobs.restore', $job->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    <button class="btn btn-success btn-sm"
                                            type="submit">

                                        <i class="fas fa-undo"></i>
                                        استعادة

                                    </button>

                                </form>


                                <form action="{{ route('service-jobs.force-delete', $job->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            type="submit"
                                            onclick="return confirm('سيتم حذف الخدمة نهائيًا، هل أنت متأكد؟')">

                                        <i class="fas fa-trash"></i>
                                        حذف نهائي

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">
                                لا توجد خدمات محذوفة.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

         

        </div>

    </div>

</div>

@endsection