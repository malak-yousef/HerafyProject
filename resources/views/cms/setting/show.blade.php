@extends('cms.parent')

@section('title', 'تفاصيل الإعداد')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-eye me-2"></i>
                تفاصيل الإعداد
            </h4>

            <p class="text-muted mb-0">
                عرض تفاصيل إعداد الموقع
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('settings.edit', $setting) }}"
               class="btn btn-warning">

                <i class="fas fa-edit me-1"></i>
                تعديل

            </a>

            <a href="{{ route('settings.index') }}"
               class="btn btn-secondary">

                <i class="fas fa-arrow-right me-1"></i>
                العودة

            </a>

        </div>

    </div>


    {{-- Setting Details --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                معلومات الإعداد
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- ID --}}
                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <small class="text-muted d-block mb-1">
                            رقم الإعداد
                        </small>

                        <strong>
                            {{ $setting->id }}
                        </strong>

                    </div>

                </div>


                {{-- Key --}}
                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <small class="text-muted d-block mb-1">
                            مفتاح الإعداد
                        </small>

                        <span class="badge bg-secondary fs-6">
                            {{ $setting->key }}
                        </span>

                    </div>

                </div>


                {{-- Value --}}
                <div class="col-12">

                    <div class="border rounded p-3">

                        <small class="text-muted d-block mb-2">
                            قيمة الإعداد
                        </small>

                        <div class="bg-light rounded p-3">
                            {{ $setting->value ?? 'لا توجد قيمة' }}
                        </div>

                    </div>

                </div>


                {{-- Created At --}}
                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <small class="text-muted d-block mb-1">
                            تاريخ الإضافة
                        </small>

                        <strong>
                            {{ $setting->created_at?->format('Y-m-d H:i') }}
                        </strong>

                    </div>

                </div>


                {{-- Updated At --}}
                <div class="col-md-6">

                    <div class="border rounded p-3">

                        <small class="text-muted d-block mb-1">
                            آخر تعديل
                        </small>

                        <strong>
                            {{ $setting->updated_at?->format('Y-m-d H:i') }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection