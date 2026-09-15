@extends('cms.parent')

@section('title', 'مراجعة طلب التوثيق')
@section('main_content', 'مراجعة طلب التوثيق')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="cms-form-title mb-1"><i class="fas fa-user-check text-warning" aria-hidden="true"></i> مراجعة طلب التوثيق</h4>
        <p class="text-muted mb-0">راجع وثيقة الحرفي وحدد حالة طلب التوثيق.</p>
    </div>

    <div class="card cms-form-card">
        <div class="card-body">
            <x-cms.form-errors-summary class="mb-4" />

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <span class="form-label fw-semibold d-block">الحرفي</span>
                    <div class="cms-readonly-field">{{ $verification->craftman?->user?->name ?? 'غير متاح' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="form-label fw-semibold d-block">المهنة</span>
                    <div class="cms-readonly-field">{{ $verification->craftman?->profession?->name ?? 'غير متاحة' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="form-label fw-semibold d-block">تاريخ التقديم</span>
                    <div class="cms-readonly-field">{{ $verification->created_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</div>
                </div>
                <div class="col-md-6">
                    <span class="form-label fw-semibold d-block">الحالة الحالية</span>
                    <div class="cms-readonly-field"><x-cms.status-badge :status="$verification->status" /></div>
                </div>
                <div class="col-12">
                    <span class="form-label fw-semibold d-block">وثيقة التوثيق</span>
                    <a href="{{ asset('storage/'.$verification->document_path) }}" target="_blank" rel="noopener" class="btn btn-outline-primary cms-document-action">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                        فتح الوثيقة
                    </a>
                </div>
            </div>

            <form action="{{ route('verifications.update', $verification) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="verification-status" class="form-label fw-semibold">حالة التوثيق <span class="cms-required" aria-hidden="true">*</span></label>
                    <select id="verification-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
                        @foreach(['pending' => 'قيد المراجعة', 'approved' => 'معتمد', 'rejected' => 'مرفوض'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $verification->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-cms.form-error field="status" />
                </div>

                <div class="cms-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> تحديث الحالة</button>
                    <a href="{{ route('verifications.show', $verification) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
