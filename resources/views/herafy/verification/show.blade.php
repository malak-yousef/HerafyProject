@extends('herafy.parent')

@section('title', 'حالة التوثيق')

@section('nav-links')
<!--<li class="nav-item">
    <a class="nav-link active"
       href="{{ route('herafy.verification.show') }}">

        <i class="fas fa-user-check"></i>
        التوثيق

    </a>
</li>-->


@endsection


@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <i class="fas fa-user-check fa-3x text-warning mb-3"></i>

                        <h3 class="fw-bold">
                            حالة التوثيق
                        </h3>

                    </div>


                    @if(!$verification)

                        <div class="text-center py-4">

                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>

                            <h5>
                                لم تقدم طلب توثيق حتى الآن
                            </h5>

                            <p class="text-muted">
                                يمكنك تقديم طلب توثيق حسابك الآن.
                            </p>

                            <a href="{{ route('herafy.verification.create') }}"
                               class="btn btn-warning">

                                <i class="fas fa-plus"></i>
                                تقديم طلب التوثيق

                            </a>

                        </div>

                    @else

                        <div class="text-center mb-4">

                            @if($verification->status === 'pending')

                                <span class="badge bg-warning text-dark fs-6 p-2">
                                    <i class="fas fa-clock"></i>
                                    قيد المراجعة
                                </span>

                            @elseif($verification->status === 'approved')

                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-check-circle"></i>
                                    تم توثيق الحساب
                                </span>

                            @elseif($verification->status === 'rejected')

                                <span class="badge bg-danger fs-6 p-2">
                                    <i class="fas fa-times-circle"></i>
                                    تم رفض طلب التوثيق
                                </span>

                            @endif

                        </div>


                        <div class="row g-3">

                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <small class="text-muted">
                                        رقم الطلب
                                    </small>

                                    <div class="fw-bold">
                                        #{{ $verification->id }}
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <small class="text-muted">
                                        تاريخ التقديم
                                    </small>

                                    <div class="fw-bold">
                                        {{ $verification->created_at->format('Y-m-d H:i') }}
                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="mt-4">

                            <a href="{{ asset('storage/' . $verification->document_path) }}"
                               target="_blank"
                               class="btn btn-outline-primary w-100">

                                <i class="fas fa-file-alt"></i>
                                عرض الوثيقة المرفوعة

                            </a>

                        </div>


                        @if($verification->status === 'rejected')

                            <div class="alert alert-danger mt-4">

                                <i class="fas fa-info-circle"></i>

                                تم رفض طلب التوثيق.
                                يمكنك تقديم طلب توثيق جديد بالوثيقة الصحيحة.

                            </div>

                            <a href="{{ route('herafy.verification.create') }}"
                               class="btn btn-warning w-100">

                                <i class="fas fa-redo"></i>
                                تقديم طلب جديد

                            </a>

                        @endif

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection