<x-cms.form-errors-summary class="mb-4" />

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-user-shield text-warning" aria-hidden="true"></i> بيانات الحساب</h5>
    <div class="row g-3">
        @foreach([
            ['name', 'الاسم الكامل', 'text'],
            ['user_name', 'اسم المستخدم', 'text'],
            ['email', 'البريد الإلكتروني', 'email'],
        ] as [$field, $label, $type])
            <div class="col-md-6">
                <label for="craftman-{{ $field }}" class="form-label fw-semibold">{{ $label }} <span class="cms-required" aria-hidden="true">*</span></label>
                <input id="craftman-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $craftmen->user->{$field} ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has($field)]) required>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach

        <div class="col-md-6">
            <label for="craftman-password" class="form-label fw-semibold">كلمة المرور @isset($craftmen)<small class="text-muted">(اختياري)</small>@else<span class="cms-required" aria-hidden="true">*</span>@endisset</label>
            <input id="craftman-password" type="password" name="password" @class(['form-control', 'is-invalid' => $errors->has('password')]) @if(! isset($craftmen)) required @endif autocomplete="new-password">
            @isset($craftmen)<small class="text-muted">اتركها فارغة للاحتفاظ بكلمة المرور الحالية.</small>@endisset
            <x-cms.form-error field="password" />
        </div>

        <div class="col-md-6">
            <label for="craftman-status" class="form-label fw-semibold">حالة الحساب <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="craftman-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
                @foreach(['pending' => 'قيد الانتظار', 'active' => 'نشط', 'inactive' => 'غير نشط', 'blocked' => 'محظور'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $craftmen->user->status ?? 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="status" />
        </div>

        <div class="col-md-6">
            <label for="craftman-gender" class="form-label fw-semibold">الجنس <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="craftman-gender" name="gender" @class(['form-select', 'is-invalid' => $errors->has('gender')]) required>
                <option value="">اختر الجنس</option>
                <option value="male" @selected(old('gender', $craftmen->user->gender ?? '') === 'male')>ذكر</option>
                <option value="female" @selected(old('gender', $craftmen->user->gender ?? '') === 'female')>أنثى</option>
            </select>
            <x-cms.form-error field="gender" />
        </div>

        @foreach([['mobile', 'رقم الهاتف', 'tel'], ['address', 'العنوان', 'text']] as [$field, $label, $type])
            <div class="col-md-6">
                <label for="craftman-{{ $field }}" class="form-label fw-semibold">{{ $label }} <small class="text-muted">(اختياري)</small></label>
                <input id="craftman-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $craftmen->user->{$field} ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has($field)])>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-screwdriver-wrench text-warning" aria-hidden="true"></i> البيانات المهنية</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="craftman-id-number" class="form-label fw-semibold">رقم الهوية <span class="cms-required" aria-hidden="true">*</span></label>
            <input id="craftman-id-number" type="text" inputmode="numeric" name="id_number" value="{{ old('id_number', $craftmen->id_number ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has('id_number')]) required>
            <x-cms.form-error field="id_number" />
        </div>
        <div class="col-md-6">
            <label for="craftman-profession" class="form-label fw-semibold">المهنة <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="craftman-profession" name="profession_id" @class(['form-select', 'is-invalid' => $errors->has('profession_id')]) required>
                <option value="">اختر المهنة</option>
                @foreach($professions as $profession)
                    <option value="{{ $profession->id }}" @selected((string) old('profession_id', $craftmen->profession_id ?? '') === (string) $profession->id)>{{ $profession->name }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="profession_id" />
        </div>
        @foreach([
            ['experience_years', 'سنوات الخبرة', '1'],
            ['hourly_rate', 'السعر بالساعة (₪)', '0.01'],
            ['daily_rate', 'السعر باليوم (₪)', '0.01'],
        ] as [$field, $label, $step])
            <div class="col-md-4">
                <label for="craftman-{{ $field }}" class="form-label fw-semibold">{{ $label }} <span class="cms-required" aria-hidden="true">*</span></label>
                <input id="craftman-{{ $field }}" type="number" step="{{ $step }}" name="{{ $field }}" value="{{ old($field, $craftmen->{$field} ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has($field)]) required>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach
        <div class="col-12">
            <label for="craftman-description" class="form-label fw-semibold">الوصف المهني <small class="text-muted">(اختياري)</small></label>
            <textarea id="craftman-description" name="description" rows="4" @class(['form-control', 'is-invalid' => $errors->has('description')])>{{ old('description', $craftmen->description ?? '') }}</textarea>
            <x-cms.form-error field="description" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-star text-warning" aria-hidden="true"></i> الظهور في الصفحة الرئيسية</h5>
    <div class="form-check form-switch">
        <input type="hidden" name="is_featured" value="0">
        <input id="craftman-is-featured" class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $craftmen->is_featured ?? false))>
        <label class="form-check-label fw-semibold" for="craftman-is-featured">إظهار الحرفي ضمن أبرز الحرفيين</label>
    </div>
    <small class="text-muted d-block mt-2">يمكن للمدير اختيار 4 حرفيين كحد أقصى، ولن يظهر إلا الحساب النشط.</small>
    <x-cms.form-error field="is_featured" />
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-image text-warning" aria-hidden="true"></i> الصورة الشخصية</h5>
    @isset($craftmen)
        <div class="cms-profile-image-preview mb-2">
            @if($craftmen->user?->image)<img src="{{ $craftmen->user->profilePhotoUrl() }}" alt="الصورة الحالية للحرفي">@else<i class="fas fa-user fa-2x" aria-hidden="true"></i>@endif
        </div>
    @endisset
    <label for="craftman-image" class="form-label fw-semibold">اختيار صورة <small class="text-muted">(اختياري)</small></label>
    <input id="craftman-image" type="file" name="image" accept=".jpeg,.jpg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" @class(['form-control', 'is-invalid' => $errors->has('image')])>
    <span class="cms-file-guidance">JPEG أو PNG أو JPG أو GIF أو WEBP، بحد أقصى 2 ميجابايت.</span>
    <x-cms.form-error field="image" />
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($craftmen) ? 'تحديث البيانات' : 'حفظ الحرفي' }}</button>
    <a href="{{ route('craftmen.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
</div>
