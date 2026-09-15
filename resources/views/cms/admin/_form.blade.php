<x-cms.form-errors-summary class="mb-4" />

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-user-shield text-warning" aria-hidden="true"></i> بيانات الحساب</h5>
    <div class="row g-3">
        @foreach([
            ['name', 'الاسم الكامل', 'text', 'name'],
            ['user_name', 'اسم المستخدم', 'text', 'username'],
            ['email', 'البريد الإلكتروني', 'email', 'email'],
        ] as [$field, $label, $type, $autocomplete])
            <div class="col-md-6">
                <label for="admin-{{ $field }}" class="form-label fw-semibold">{{ $label }} <span class="cms-required" aria-hidden="true">*</span></label>
                <input id="admin-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $admins->{$field} ?? '') }}" autocomplete="{{ $autocomplete }}" @class(['form-control', 'is-invalid' => $errors->has($field)]) required>
                <x-cms.form-error :field="$field" />
            </div>
        @endforeach

        <div class="col-md-6">
            <label for="admin-mobile" class="form-label fw-semibold">رقم الهاتف <small class="text-muted">(اختياري)</small></label>
            <input id="admin-mobile" type="tel" name="mobile" value="{{ old('mobile', $admins->mobile ?? '') }}" autocomplete="tel" @class(['form-control', 'is-invalid' => $errors->has('mobile')])>
            <x-cms.form-error field="mobile" />
        </div>

        <div class="col-md-6">
            <label for="admin-address" class="form-label fw-semibold">العنوان <small class="text-muted">(اختياري)</small></label>
            <input id="admin-address" type="text" name="address" value="{{ old('address', $admins->address ?? '') }}" autocomplete="street-address" @class(['form-control', 'is-invalid' => $errors->has('address')])>
            <x-cms.form-error field="address" />
        </div>

        <div class="col-md-6">
            <label for="admin-date" class="form-label fw-semibold">تاريخ الميلاد <small class="text-muted">(اختياري)</small></label>
            <input id="admin-date" type="date" name="date" value="{{ old('date', $admins->date ?? '') }}" @class(['form-control', 'is-invalid' => $errors->has('date')])>
            <x-cms.form-error field="date" />
        </div>

        <div class="col-md-6">
            <label for="admin-gender" class="form-label fw-semibold">الجنس <small class="text-muted">(اختياري)</small></label>
            <select id="admin-gender" name="gender" @class(['form-select', 'is-invalid' => $errors->has('gender')])>
                <option value="">اختر الجنس</option>
                <option value="male" @selected(old('gender', $admins->gender ?? '') === 'male')>ذكر</option>
                <option value="female" @selected(old('gender', $admins->gender ?? '') === 'female')>أنثى</option>
            </select>
            <x-cms.form-error field="gender" />
        </div>

        <div class="col-md-6">
            <label for="admin-status" class="form-label fw-semibold">حالة الحساب <span class="cms-required" aria-hidden="true">*</span></label>
            <select id="admin-status" name="status" @class(['form-select', 'is-invalid' => $errors->has('status')]) required>
                @foreach(['active' => 'نشط', 'inactive' => 'غير نشط', 'pending' => 'قيد الانتظار', 'blocked' => 'محظور'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $admins->status ?? 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-cms.form-error field="status" />
        </div>

        <div class="col-md-6">
            <label for="admin-password" class="form-label fw-semibold">
                {{ isset($admins) ? 'كلمة المرور الجديدة' : 'كلمة المرور' }}
                @if(isset($admins))<small class="text-muted">(اختياري)</small>@else<span class="cms-required" aria-hidden="true">*</span>@endif
            </label>
            <input id="admin-password" type="password" name="password" autocomplete="new-password" @class(['form-control', 'is-invalid' => $errors->has('password')]) @required(! isset($admins))>
            @isset($admins)<small class="text-muted">اترك كلمة المرور فارغة للاحتفاظ بكلمة المرور الحالية.</small>@endisset
            <x-cms.form-error field="password" />
        </div>

        <div class="col-md-6">
            <label for="admin-password-confirmation" class="form-label fw-semibold">
                تأكيد كلمة المرور
                @if(isset($admins))<small class="text-muted">(اختياري)</small>@else<span class="cms-required" aria-hidden="true">*</span>@endif
            </label>
            <input id="admin-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" @class(['form-control', 'is-invalid' => $errors->has('password_confirmation') || $errors->has('password')]) @required(! isset($admins))>
            <x-cms.form-error field="password_confirmation" />
        </div>
    </div>
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-key text-warning" aria-hidden="true"></i> الصلاحيات الإدارية</h5>
    @if(isset($admins))
        <p class="text-muted small">تُعرض الأدوار الحالية للقراءة فقط، ولا يمكن تعديلها من هذا النموذج.</p>
        <div class="cms-readonly-field d-flex flex-wrap gap-2" data-testid="admin-readonly-roles">
            @forelse($admins->roles->where('guard_name', 'admin') as $role)
                <span class="badge bg-secondary">{{ $role->name === 'super-admin' ? 'مدير النظام الأعلى' : str_replace('-', ' ', $role->name) }}</span>
            @empty
                <span class="text-muted">لا توجد أدوار إدارية.</span>
            @endforelse
        </div>
    @else
        <label for="admin-role" class="form-label fw-semibold">الدور الإداري <span class="cms-required" aria-hidden="true">*</span></label>
        <select id="admin-role" name="role_name" @class(['form-select', 'is-invalid' => $errors->has('role_name')]) required>
            <option value="">اختر الدور الإداري</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" @selected(old('role_name') === $role->name)>{{ $role->name === 'super-admin' ? 'مدير النظام الأعلى' : str_replace('-', ' ', $role->name) }}</option>
            @endforeach
        </select>
        <x-cms.form-error field="role_name" />
    @endif
</section>

<section class="cms-form-section">
    <h5 class="cms-form-section-title"><i class="fas fa-image text-warning" aria-hidden="true"></i> الصورة الشخصية</h5>
    @isset($admins)
        <div class="cms-profile-image-preview mb-3">
            @if($admins->image)
                <img src="{{ asset('storage/images/admin/'.$admins->image) }}" alt="الصورة الحالية للمسؤول">
            @else
                <i class="fas fa-user fa-2x" aria-hidden="true"></i>
            @endif
        </div>
    @endisset
    <label for="admin-image" class="form-label fw-semibold">{{ isset($admins) ? 'استبدال الصورة' : 'الصورة الشخصية' }} <small class="text-muted">(اختياري)</small></label>
    <input id="admin-image" type="file" name="image" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" @class(['form-control', 'is-invalid' => $errors->has('image')])>
    <span class="cms-file-guidance">JPEG أو PNG أو JPG أو WEBP، بحد أقصى 2 ميجابايت.</span>
    <x-cms.form-error field="image" />
</section>

<div class="cms-form-actions mt-4">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save" aria-hidden="true"></i> {{ isset($admins) ? 'تحديث' : 'حفظ' }}</button>
    <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right" aria-hidden="true"></i> {{ isset($admins) ? 'رجوع' : 'إلغاء' }}</a>
</div>
