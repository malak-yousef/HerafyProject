@extends('cms.parent')

@section('title', 'إضافة إعداد')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-plus-circle me-2"></i>
                إضافة إعداد جديد
            </h4>

            <p class="text-muted mb-0">
                إضافة إعداد جديد إلى إعدادات منصة حرفي
            </p>
        </div>

        <a href="{{ route('settings.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-right me-1"></i>
            العودة للإعدادات

        </a>

    </div>


    {{-- Errors --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                يرجى تصحيح الأخطاء التالية:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- Form --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                بيانات الإعداد
            </h5>

        </div>

        <div class="card-body">

            <form action="{{ route('settings.store') }}"
                  method="POST">

                @csrf


                {{-- Key --}}
                <div class="mb-4">

                    <label for="key" class="form-label fw-bold">
                        مفتاح الإعداد
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="key"
                        id="key"
                        value="{{ old('key') }}"
                        class="form-control @error('key') is-invalid @enderror"
                        placeholder="مثال: max_daily_offers"
                        required
                    >

                    @error('key')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <small class="text-muted">
                        استخدم اسمًا واضحًا وفريدًا للإعداد.
                    </small>

                </div>


                {{-- Value --}}
                <div class="mb-4">

                    <label for="value" class="form-label fw-bold">
                        قيمة الإعداد
                    </label>

                    <textarea
                        name="value"
                        id="value"
                        rows="4"
                        class="form-control @error('value') is-invalid @enderror"
                        placeholder="أدخل قيمة الإعداد..."
                    >{{ old('value') }}</textarea>

                    @error('value')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('settings.index') }}"
                       class="btn btn-secondary">

                        <i class="fas fa-times me-1"></i>
                        إلغاء

                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save me-1"></i>
                        حفظ الإعداد

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection