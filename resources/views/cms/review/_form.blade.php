<x-cms.form-errors-summary class="mb-4" />

@isset($review)
    <div class="alert alert-info">بيانات التقييم الأصلية للقراءة فقط حفاظًا على المصداقية. يمكن للإدارة التحكم بظهوره أو إخفائه فقط.</div>
@endisset

<div class="row g-3">
    <div class="col-md-6">
        <label for="review-craftsman" class="form-label fw-semibold">الحرفي <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="review-craftsman" name="craftsman_id" @class(['form-select', 'is-invalid' => $errors->has('craftsman_id')]) required @disabled(isset($review))>
            <option value="">اختر الحرفي</option>
            @foreach($craftmen as $craftman)
                <option value="{{ $craftman->id }}" @selected((string) old('craftsman_id', $review->craftsman_id ?? '') === (string) $craftman->id)>{{ $craftman->user?->name ?? 'غير متاح' }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="craftsman_id" />
    </div>

    <div class="col-md-6">
        <label for="review-customer" class="form-label fw-semibold">العميل <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="review-customer" name="customer_id" @class(['form-select', 'is-invalid' => $errors->has('customer_id')]) required @disabled(isset($review))>
            <option value="">اختر العميل</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id', $review->customer_id ?? '') === (string) $customer->id)>{{ $customer->user?->name ?? 'غير متاح' }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="customer_id" />
    </div>

    <div class="col-md-6">
        <label for="review-request" class="form-label fw-semibold">الطلب <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="review-request" name="request_id" @class(['form-select', 'is-invalid' => $errors->has('request_id')]) required @disabled(isset($review))>
            <option value="">اختر الطلب</option>
            @foreach($requests as $order)
                <option value="{{ $order->id }}" @selected((string) old('request_id', $review->request_id ?? '') === (string) $order->id)>
                    {{ $order->title }} ({{ $order->customer?->user?->name ?? 'عميل غير متاح' }})
                </option>
            @endforeach
        </select>
        <x-cms.form-error field="request_id" />
    </div>

    <div class="col-md-6">
        <label for="review-rating" class="form-label fw-semibold">التقييم <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="review-rating" name="rating" @class(['form-select', 'is-invalid' => $errors->has('rating')]) required @disabled(isset($review))>
            <option value="">اختر التقييم</option>
            @foreach([1 => 'نجمة واحدة', 2 => 'نجمتان', 3 => '3 نجوم', 4 => '4 نجوم', 5 => '5 نجوم'] as $value => $label)
                <option value="{{ $value }}" @selected((string) old('rating', $review->rating ?? '') === (string) $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="rating" />
    </div>

    @isset($review)
        <div class="col-md-6">
            <label for="review-status" class="form-label fw-semibold">حالة التقييم <small class="text-muted">(اختياري)</small></label>
            <select id="review-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')])>
                @foreach(['approved' => 'ظاهر', 'rejected' => 'مخفي'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $review->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="status" />
        </div>
    @endisset

    <div class="col-12">
        <label for="review-comment" class="form-label fw-semibold">التعليق <small class="text-muted">(اختياري)</small></label>
        <textarea id="review-comment" name="comment" rows="5" @class(['form-control', 'is-invalid' => $errors->has('comment')]) placeholder="اكتب ملاحظاتك على الخدمة المقدمة" @readonly(isset($review))>{{ old('comment', $review->comment ?? '') }}</textarea>
        <x-cms.form-error field="comment" />
    </div>
</div>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save" aria-hidden="true"></i>
        {{ isset($review) ? 'حفظ حالة الظهور' : 'حفظ التقييم' }}
    </button>
    <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-right" aria-hidden="true"></i>
        إلغاء
    </a>
</div>
