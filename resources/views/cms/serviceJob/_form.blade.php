<x-cms.form-errors-summary class="mb-4" />

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-link text-warning" aria-hidden="true"></i> العلاقات</h5>
    <p class="text-muted small">يجب أن يكون العرض تابعًا للطلب، وأن يكون العميل والحرفي هما الطرفين المرتبطين بهما.</p>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="service-job-order" class="form-label fw-semibold">الطلب <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="service-job-order" name="order_id" @class(['form-select', 'is-invalid' => $errors->has('order_id')]) required>
                <option value="">اختر الطلب</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" data-customer-id="{{ $order->customer_id }}" @selected((string) old('order_id', $serviceJob->order_id ?? '') === (string) $order->id)>{{ $order->title }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="order_id" />
        </div>
        <div class="col-md-6">
            <label for="service-job-offer" class="form-label fw-semibold">العرض <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="service-job-offer" name="offer_id" @class(['form-select', 'is-invalid' => $errors->has('offer_id')]) required>
                <option value="">اختر العرض</option>
                @foreach($offers as $offerOption)
                    <option value="{{ $offerOption->id }}" data-order-id="{{ $offerOption->order_id }}" data-craftman-id="{{ $offerOption->craftman_id }}" @selected((string) old('offer_id', $serviceJob->offer_id ?? '') === (string) $offerOption->id)>{{ $offerOption->order?->title ?? 'طلب غير متاح' }} — {{ $offerOption->craftman?->user?->name ?? 'حرفي غير متاح' }} — {{ $offerOption->price }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="offer_id" />
        </div>
        <div class="col-md-6">
            <label for="service-job-customer" class="form-label fw-semibold">العميل <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="service-job-customer" name="customer_id" @class(['form-select', 'is-invalid' => $errors->has('customer_id')]) required>
                <option value="">اختر العميل</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected((string) old('customer_id', $serviceJob->customer_id ?? '') === (string) $customer->id)>{{ $customer->user?->name ?? 'غير متاح' }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="customer_id" />
        </div>
        <div class="col-md-6">
            <label for="service-job-craftman" class="form-label fw-semibold">الحرفي <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="service-job-craftman" name="craftman_id" @class(['form-select', 'is-invalid' => $errors->has('craftman_id')]) required>
                <option value="">اختر الحرفي</option>
                @foreach($craftsmen as $craftsman)
                    <option value="{{ $craftsman->id }}" @selected((string) old('craftman_id', $serviceJob->craftman_id ?? '') === (string) $craftsman->id)>{{ $craftsman->user?->name ?? 'غير متاح' }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="craftman_id" />
        </div>
    </div>
</section>

<p class="text-muted small mt-3 mb-0">يتم تضييق الخيارات تلقائيًا حسب الطلب والعرض المختارين.</p>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-circle-info text-warning" aria-hidden="true"></i> حالة العمل</h5>
    <div class="row g-3"><div class="col-md-6">
        <label for="service-job-status" class="form-label fw-semibold">الحالة <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="service-job-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
            @foreach(['pending' => 'قيد الانتظار', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغى'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $serviceJob->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="status" />
    </div></div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-clock text-warning" aria-hidden="true"></i> التوقيت</h5>
    <p class="text-muted small">وقت البدء مطلوب عند التنفيذ أو الاكتمال، ووقت الإنجاز مطلوب فقط للحالة المكتملة ولا يسبق وقت البدء.</p>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="service-job-started-at" class="form-label fw-semibold">وقت البدء <small class="text-muted">(حسب الحالة)</small></label>
            <input id="service-job-started-at" type="datetime-local" name="started_at" value="{{ old('started_at', isset($serviceJob) ? $serviceJob->started_at?->format('Y-m-d\TH:i') : '') }}" @class(['form-control', 'is-invalid' => $errors->has('started_at')])>
            <x-cms.form-error field="started_at" />
        </div>
        <div class="col-md-6">
            <label for="service-job-completed-at" class="form-label fw-semibold">وقت الإنجاز <small class="text-muted">(للحالة المكتملة)</small></label>
            <input id="service-job-completed-at" type="datetime-local" name="completed_at" value="{{ old('completed_at', isset($serviceJob) ? $serviceJob->completed_at?->format('Y-m-d\TH:i') : '') }}" @class(['form-control', 'is-invalid' => $errors->has('completed_at')])>
            <x-cms.form-error field="completed_at" />
        </div>
    </div>
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($serviceJob) ? 'تحديث' : 'حفظ' }}</button>
    <a href="{{ route('service-jobs.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
</div>
