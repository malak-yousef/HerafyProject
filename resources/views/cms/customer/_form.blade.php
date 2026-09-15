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
                <label for="customer-{{ $field }}" class="form-label fw-semibold">{{ $label }} <span class="cms-required" aria-hidden="true">*</span></label>
                <input id="customer-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $customers->user->{$field} ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has($field)]) required>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach

        <div class="col-md-6">
            <label for="customer-password" class="form-label fw-semibold">
                كلمة المرور
                @isset($customers)<small class="text-muted">(اختياري)</small>@else<span class="cms-required" aria-hidden="true">*</span>@endisset
            </label>
            <input id="customer-password" type="password" name="password" @class(['form-control', 'is-invalid' => $errors->has('password')]) @if(! isset($customers)) required @endif autocomplete="new-password">
            @isset($customers)<small class="text-muted">اتركها فارغة للاحتفاظ بكلمة المرور الحالية.</small>@endisset
            <x-cms.form-error field="password" />
        </div>

        <div class="col-md-6">
            <label for="customer-status" class="form-label fw-semibold">حالة الحساب <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="customer-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
                @foreach(['pending' => 'قيد الانتظار', 'active' => 'نشط', 'inactive' => 'غير نشط', 'blocked' => 'محظور'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $customers->user->status ?? 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="status" />
        </div>

        <div class="col-md-6">
            <label for="customer-gender" class="form-label fw-semibold">الجنس <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="customer-gender" name="gender" @class(['form-select', 'is-invalid' => $errors->has('gender')]) required>
                <option value="">اختر الجنس</option>
                <option value="male" @selected(old('gender', $customers->user->gender ?? '') === 'male')>ذكر</option>
                <option value="female" @selected(old('gender', $customers->user->gender ?? '') === 'female')>أنثى</option>
            </select>
            <x-cms.form-error field="gender" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-address-card text-warning" aria-hidden="true"></i> بيانات الملف الشخصي</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="customer-id-number" class="form-label fw-semibold">رقم الهوية <span class="cms-required" aria-hidden="true">*</span></label>
            <input id="customer-id-number" type="text" inputmode="numeric" name="id_number" value="{{ old('id_number', $customers->id_number ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has('id_number')]) required>
            <x-cms.form-error field="id_number" />
        </div>
        @foreach([
            ['mobile', 'رقم الهاتف', 'tel'],
            ['address', 'العنوان', 'text'],
            ['date', 'تاريخ الميلاد', 'date'],
        ] as [$field, $label, $type])
            <div class="col-md-6">
                <label for="customer-{{ $field }}" class="form-label fw-semibold">{{ $label }} <small class="text-muted">(اختياري)</small></label>
                <input id="customer-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $customers->user->{$field} ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has($field)])>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach

        <div class="col-12">
            <label for="customer-image" class="form-label fw-semibold">الصورة الشخصية <small class="text-muted">(اختياري)</small></label>
            @isset($customers)
                <div class="cms-profile-image-preview mb-2">
                    @if($customers->user?->image)<img src="{{ $customers->user->profilePhotoUrl() }}" alt="الصورة الحالية للعميل">@else<i class="fas fa-user fa-2x" aria-hidden="true"></i>@endif
                </div>
            @endisset
            <input id="customer-image" type="file" name="image" accept=".jpeg,.jpg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" @class(['form-control', 'is-invalid' => $errors->has('image')])>
            <span class="cms-file-guidance">JPEG أو PNG أو JPG أو GIF أو WEBP، بحد أقصى 2 ميجابايت.</span>
            <x-cms.form-error field="image" />
        </div>
    </div>
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($customers) ? 'تحديث البيانات' : 'حفظ العميل' }}</button>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> إلغاء</a>
</div>
