@extends('cms.parent')

@section('title', 'طلبات التوثيق')

@section('main_content', 'طلبات التوثيق')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-user-check text-warning"></i>
                طلبات التوثيق
            </h4>

            <p class="text-muted mb-0">
                إدارة ومراجعة طلبات توثيق الحرفيين
            </p>
        </div>

    </div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <div class="card shadow-sm border-0">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle text-center">

                    <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>الحرفي</th>
                            <th>الوثيقة</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($verifications as $verification)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                   {{ $verification->craftman->user->name ?? 'غير معروف' }}
                                </td>

                                <td>

                                    <a href="{{ asset('storage/' . $verification->document_path) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="fas fa-file-alt"></i>
                                        عرض الوثيقة

                                    </a>

                                </td>

                                <td>

                                    @if($verification->status === 'pending')

                                        <span class="badge bg-warning text-dark">
                                            قيد المراجعة
                                        </span>

                                    @elseif($verification->status === 'approved')

                                        <span class="badge bg-success">
                                            مقبول
                                        </span>

                                    @elseif($verification->status === 'rejected')

                                        <span class="badge bg-danger">
                                            مرفوض
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $verification->created_at->format('Y-m-d') }}
                                </td>

                                <td>

                                    <a href="{{ route('verifications.show', $verification->id) }}"
                                       class="btn btn-sm btn-info text-white">

                                        <i class="fas fa-eye"></i>
                                        عرض

                                    </a>

                                    <a href="{{ route('verifications.edit', $verification->id) }}"
                                       class="btn btn-sm btn-warning">

                                        <i class="fas fa-edit"></i>
                                        مراجعة

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-muted py-4">

                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                    <p class="mb-0">
                                        لا توجد طلبات توثيق حاليًا
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $verifications->links() }}

            </div>

        </div>

    </div>

</div>

@endsection