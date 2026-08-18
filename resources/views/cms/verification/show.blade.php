@extends('cms.parent')

@section('title', 'تفاصيل طلب التوثيق')

@section('main_content', 'تفاصيل طلب التوثيق')

@section('content')

<div class="container-fluid">

    {{-- عنوان الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-user-check text-warning"></i>
                تفاصيل طلب التوثيق
            </h4>

            <p class="text-muted mb-0">
                مراجعة بيانات طلب توثيق الحرفي
            </p>
        </div>

        <div>
            <a href="{{ route('verifications.index') }}"
               class="btn btn-secondary">

                <i class="fas fa-arrow-right"></i>
                العودة للطلبات

            </a>
        </div>

    </div>


    {{-- رسالة النجاح --}}
    @if(session('success'))

        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>

    @endif


    <div class="row g-4">

        {{-- بيانات الحرفي --}}
        <div class="col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user text-warning"></i>
                        بيانات الحرفي
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="fw-bold">
                            اسم الحرفي
                        </label>

                        <p class="text-muted mb-0">
{{ $verification->craftman->user->name ?? 'غير معروف' }}                        </p>

                    </div>


                    <div class="mb-3">

                        <label class="fw-bold">
                            رقم الحرفي
                        </label>

                        <p class="text-muted mb-0">
                            #{{ $verification->craftman->user->id ?? '-' }}
                        </p>

                    </div>


                    <div class="mb-3">

                        <label class="fw-bold">
                            المهنة
                        </label>

                        <p class="text-muted mb-0">

                            {{ $verification->craftman->user->name ?? 'غير معروف' }}

                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- بيانات التوثيق --}}
        <div class="col-md-6">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-header bg-white">

                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-file-signature text-warning"></i>
                        بيانات التوثيق
                    </h5>

                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="fw-bold">
                            رقم الطلب
                        </label>

                        <p class="text-muted mb-0">
                            #{{ $verification->id }}
                        </p>

                    </div>


                    <div class="mb-3">

                        <label class="fw-bold">
                            تاريخ التقديم
                        </label>

                        <p class="text-muted mb-0">

                            {{ $verification->created_at->format('Y-m-d H:i') }}

                        </p>

                    </div>


                    <div class="mb-3">

                        <label class="fw-bold">
                            الحالة
                        </label>

                        <div>

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

                        </div>

                    </div>


                    <div class="mt-4">

                        <a href="{{ asset('storage/' . $verification->document_path) }}"
                           target="_blank"
                           class="btn btn-primary w-100">

                            <i class="fas fa-file-alt"></i>
                            فتح وثيقة التوثيق

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- أزرار الإجراءات --}}
    <div class="card shadow-sm border-0 mt-4">

        <div class="card-body">

            <h5 class="fw-bold mb-3">

                <i class="fas fa-cogs text-warning"></i>
                إجراءات التوثيق

            </h5>


            <div class="d-flex gap-2">

                <a href="{{ route('verifications.edit', $verification->id) }}"
                   class="btn btn-warning">

                    <i class="fas fa-edit"></i>
                    مراجعة الطلب

                </a>


                <form action="{{ route('verifications.destroy', $verification->id) }}"
                      method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف طلب التوثيق؟');">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="btn btn-danger">

                        <i class="fas fa-trash"></i>
                        حذف الطلب

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection