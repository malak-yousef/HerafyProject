@extends('cms.parent')

@section('title', 'الخدمات')

@section('main_content', 'الخدمات')

@section('content')

<div class="container-fluid">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-briefcase"></i>
                الخدمات
            </h5>

            <div>
                <a href="{{ route('service-jobs.create') }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    إضافة خدمة
                </a>

                <a href="{{ route('service-jobs.trashed') }}"
                   class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    المحذوفات
                </a>
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover text-center align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الطلب</th>
                            <th>العرض</th>
                            <th>العميل</th>
                            <th>الحرفي</th>
                            <th>الحالة</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الإنجاز</th>
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
                                #{{ $job->offer_id }}
                            </td>

                            <td>
                                {{ $job->customer->user->name ?? '-' }}
                            </td>

                            <td>
{{ $job->craftman->user->name ?? '-' }}                            </td>

                            <td>

                                @if($job->status === 'pending')
                                    <span class="badge bg-warning">
                                        قيد الانتظار
                                    </span>

                                @elseif($job->status === 'in_progress')
                                    <span class="badge bg-primary">
                                        قيد التنفيذ
                                    </span>

                                @elseif($job->status === 'completed')
                                    <span class="badge bg-success">
                                        مكتملة
                                    </span>

                                @elseif($job->status === 'cancelled')
                                    <span class="badge bg-danger">
                                        ملغاة
                                    </span>
                                @endif

                            </td>

                            <td>
                                {{ $job->started_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>

                            <td>
                                {{ $job->completed_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>

                            <td>

                                <a href="{{ route('service-jobs.show', $job) }}"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('service-jobs.edit', $job) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('service-jobs.destroy', $job) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('هل أنت متأكد من نقل الخدمة إلى المحذوفات؟')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="text-center">
                                لا توجد خدمات.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">
                {{ $jobs->links() }}
            </div>

        </div>
    </div>

</div>

@endsection