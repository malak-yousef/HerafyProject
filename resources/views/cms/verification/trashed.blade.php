@extends('cms.parent')

@section('title', 'التوثيقات المحذوفة')

@section('main_content', 'التوثيقات المحذوفة')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-trash text-danger"></i>
                التوثيقات المحذوفة
            </h4>

            <p class="text-muted mb-0">
                إدارة طلبات التوثيق المحذوفة
            </p>
        </div>

        <a href="{{ route('verifications.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-right"></i>
            العودة للتوثيقات

        </a>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
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
                            <th>الحالة</th>
                            <th>تاريخ الحذف</th>
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
{{ $verification->craftman->user->name ?? 'غير معروف' }}                                </td>

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
                                    {{ $verification->deleted_at->format('Y-m-d H:i') }}
                                </td>

                                <td>

                                    <form action="{{ route('verifications.restore', $verification->id) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-sm btn-success">

                                            <i class="fas fa-trash-restore"></i>
                                            استعادة

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="py-4 text-muted">

                                    <i class="fas fa-trash fa-2x mb-2"></i>

                                    <p class="mb-0">
                                        لا توجد توثيقات محذوفة
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