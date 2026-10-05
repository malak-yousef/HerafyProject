<x-cms.form-errors-summary class="mb-4" />

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-file-invoice-dollar text-warning" aria-hidden="true"></i> بيانات العرض</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="offer-order" class="form-label fw-semibold">الطلب <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="offer-order" name="order_id" @class(['form-select', 'is-invalid' => $errors->has('order_id')]) required>
                <option value="">اختر الطلب</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" data-profession-id="{{ $order->profession_id }}" @selected((string) old('order_id', $offer->order_id ?? $selectedOrderId ?? '') === (string) $order->id)>{{ $order->title }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="order_id" />
        </div>
        <div class="col-md-6">
            <label for="offer-craftman" class="form-label fw-semibold">الحرفي <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="offer-craftman" name="craftman_id" @class(['form-select', 'is-invalid' => $errors->has('craftman_id')]) required>
                <option value="">اختر الحرفي</option>
                @foreach($craftmen as $craftman)
                    <option value="{{ $craftman->id }}" data-profession-id="{{ $craftman->profession_id }}" @selected((string) old('craftman_id', $offer->craftman_id ?? '') === (string) $craftman->id)>{{ $craftman->user?->name ?? 'غير متاح' }}</option>
                @endforeach
            </select>
            <small class="text-muted">يتم عرض الحرفيين الذين تتوافق مهنتهم مع مهنة الطلب المختار.</small>
            <x-cms.form-error field="craftman_id" />
        </div>
        <div class="col-md-6">
            <label for="offer-price" class="form-label fw-semibold">السعر المقترح <span class="cms-required" aria-hidden="true">*</span></label>
            <input id="offer-price" type="number" step="0.01" min="1" name="price" value="{{ old('price', $offer->price ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has('price')]) required>
            <x-cms.form-error field="price" />
        </div>
        <div class="col-12">
            <label for="offer-message" class="form-label fw-semibold">رسالة العرض <span class="cms-required" aria-hidden="true">*</span></label>
            <textarea id="offer-message" name="message" rows="5" @class(['form-control', 'is-invalid' => $errors->has('message')]) placeholder="اكتب تفاصيل العرض" required>{{ old('message', $offer->message ?? '') }}</textarea>
            <x-cms.form-error field="message" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-circle-info text-warning" aria-hidden="true"></i> الحالة</h5>
    <div class="row g-3"><div class="col-md-6">
        <label for="offer-status" class="form-label fw-semibold">حالة العرض <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="offer-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
            @foreach(['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغى'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $offer->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="status" />
    </div></div>
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($offer) ? 'تحديث' : 'حفظ' }}</button>
    <a href="{{ route('offers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
</div>
