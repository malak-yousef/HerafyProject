@extends('cms.parent')

@section('title', 'تعديل الإعداد')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="fas fa-edit me-2"></i>
                تعديل الإعداد
            </h4>

            <p class="text-muted mb-0">
                تعديل بيانات الإعداد
            </p>
        </div>

        <a href="{{ route('settings.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-right me-1"></i>
            العودة للإعدادات

        </a>

    </div>


    {{-- Form --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                تعديل: {{ $setting->key }}
            </h5>

        </div>

        <div class="card-body">

            <form action="{{ route('settings.update', $setting) }}"
                  method="POST">

                @csrf
                @method('PUT')


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
                        value="{{ old('key', $setting->key) }}"
                        class="form-control @error('key') is-invalid @enderror"
                        required
                    >

                    @error('key')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

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
                    >{{ old('value', $setting->value) }}</textarea>

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
                            class="btn btn-warning">

                        <i class="fas fa-save me-1"></i>
                        حفظ التعديلات

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection