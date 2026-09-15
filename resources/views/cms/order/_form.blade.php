<x-cms.form-errors-summary class="mb-4" />

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-clipboard-list text-warning" aria-hidden="true"></i> بيانات الطلب</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="order-customer" class="form-label fw-semibold">العميل <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="order-customer" name="customer_id" @class(['form-select', 'is-invalid' => $errors->has('customer_id')]) required>
                <option value="">اختر العميل</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected((string) old('customer_id', $order->customer_id ?? '') === (string) $customer->id)>{{ $customer->user?->name ?? 'غير متاح' }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="customer_id" />
        </div>
        <div class="col-md-6">
            <label for="order-profession" class="form-label fw-semibold">المهنة <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="order-profession" name="profession_id" @class(['form-select', 'is-invalid' => $errors->has('profession_id')]) required>
                <option value="">اختر المهنة</option>
                @foreach($professions as $profession)
                    <option value="{{ $profession->id }}" @selected((string) old('profession_id', $order->profession_id ?? '') === (string) $profession->id)>{{ $profession->name }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="profession_id" />
        </div>
        <div class="col-12">
            <label for="order-title" class="form-label fw-semibold">عنوان الطلب <span class="cms-required" aria-hidden="true">*</span></label>
            <input id="order-title" type="text" name="title" value="{{ old('title', $order->title ?? '') }}" maxlength="255" @class(['form-control', 'is-invalid' => $errors->has('title')]) placeholder="مثال: صيانة تمديدات المياه" required>
            <x-cms.form-error field="title" />
        </div>
        <div class="col-12">
            <label for="order-description" class="form-label fw-semibold">وصف الطلب <span class="cms-required" aria-hidden="true">*</span></label>
            <textarea id="order-description" name="description" rows="5" @class(['form-control', 'is-invalid' => $errors->has('description')]) placeholder="اكتب تفاصيل الخدمة المطلوبة" required>{{ old('description', $order->description ?? '') }}</textarea>
            <x-cms.form-error field="description" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-location-dot text-warning" aria-hidden="true"></i> الموقع</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="cms-order-city" class="form-label fw-semibold">المدينة <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="cms-order-city" name="city_id" @class(['form-select', 'is-invalid' => $errors->has('city_id')]) required>
                <option value="">اختر المدينة</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" @selected((string) old('city_id', $order->city_id ?? '') === (string) $city->id)>{{ $city->name }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="city_id" />
        </div>
        <div class="col-md-6">
            <label for="cms-order-area" class="form-label fw-semibold">المنطقة <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="cms-order-area" name="area_id" @class(['form-select', 'is-invalid' => $errors->has('area_id')]) required>
                <option value="">اختر المدينة أولاً</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" data-city="{{ $area->city_id }}" @selected((string) old('area_id', $order->area_id ?? '') === (string) $area->id)>{{ $area->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">تظهر المناطق التابعة للمدينة المحددة فقط.</small>
            <x-cms.form-error field="area_id" />
        </div>
        <div class="col-md-6">
            <label for="order-address" class="form-label fw-semibold">العنوان @isset($order)<span class="cms-required" aria-hidden="true">*</span>@else<small class="text-muted">(اختياري)</small>@endisset</label>
            <input id="order-address" type="text" name="address" value="{{ old('address', $order->address ?? '') }}" maxlength="255" @class(['form-control', 'is-invalid' => $errors->has('address')]) @isset($order) required @endisset>
            <x-cms.form-error field="address" />
        </div>
        <div class="col-md-6">
            <label for="order-phone" class="form-label fw-semibold">رقم الهاتف <small class="text-muted">(اختياري)</small></label>
            <input id="order-phone" type="tel" name="phone" value="{{ old('phone', $order->phone ?? '') }}" maxlength="20" @class(['form-control', 'is-invalid' => $errors->has('phone')])>
            <x-cms.form-error field="phone" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-calendar-check text-warning" aria-hidden="true"></i> الميزانية والموعد</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="order-budget" class="form-label fw-semibold">الميزانية <small class="text-muted">(اختياري)</small></label>
            <input id="order-budget" type="number" step="any" name="budget" value="{{ old('budget', $order->budget ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has('budget')]) placeholder="مثال: 100">
            <x-cms.form-error field="budget" />
        </div>
        <div class="col-md-6">
            <label for="order-service-date" class="form-label fw-semibold">تاريخ الخدمة <small class="text-muted">(اختياري)</small></label>
            <input id="order-service-date" type="date" name="service_date" value="{{ old('service_date', isset($order) ? $order->service_date?->format('Y-m-d') : '') }}" @class(['form-control', 'is-invalid' => $errors->has('service_date')])>
            <x-cms.form-error field="service_date" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-circle-info text-warning" aria-hidden="true"></i> الحالة</h5>
    <div class="row g-3"><div class="col-md-6">
        <label for="order-status" class="form-label fw-semibold">حالة الطلب @isset($order)<span class="cms-required" aria-hidden="true">*</span>@else<small class="text-muted">(اختياري)</small>@endisset</label>
        <select id="order-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) @isset($order) required @endisset>
            @foreach(['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $order->status ?? 'pending') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="status" />
    </div></div>
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($order) ? 'تحديث' : 'حفظ' }}</button>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
</div>
