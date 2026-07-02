@extends('cms.parent')

@section('title', 'اضافة حرفي')


@section('styles')
<style>
.form-card {
    background: #fff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.form-card .form-label {
    font-weight: 600;
    color: #2d3436;
}

.form-card .form-control,
.form-card .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-card .form-control:focus,
.form-card .form-select:focus {
    border-color: #FFC107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.form-card .form-control.is-invalid {
    border-color: #e17055;
}

.form-card .form-control.is-valid {
    border-color: #00b894;
}

.form-card .invalid-feedback {
    font-size: 13px;
    color: #e17055;
}

.form-card .btn-submit {
    padding: 12px 40px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.form-card .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

.form-card .btn-back {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.form-card .btn-back:hover {
    transform: translateY(-2px);
}

.image-preview {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    border: 2px dashed #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    background: #f8f9fa;
}

.image-preview:hover {
    border-color: #FFC107;
    background: #fffdf5;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview .placeholder {
    color: #adb5bd;
    text-align: center;
}

.image-preview .placeholder i {
    font-size: 2rem;
    display: block;
    margin-bottom: 5px;
}

.image-preview .placeholder span {
    font-size: 12px;
}

@media (max-width: 768px) {
    .form-card {
        padding: 20px;
    }

    .image-preview {
        width: 120px;
        height: 120px;
    }
}
</style>
@endsection

@section('main_content' , 'اضافة حرفي')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-plus text-warning"></i> إضافة حرفي جديد
                    </h5>
                    <p class="text-muted small mb-0">أدخل بيانات الحرفي الجديد لإضافته إلى المنصة</p>
                </div>
                <a href="{{ route('craftmen.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عنصر عرض الأخطاء (جديد) -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="craftmen-form" action="#" onsubmit="event.preventDefault(); performStore();">
                <div class="row g-4">
                    <!-- الصورة الشخصية -->
                    <div class="col-md-3">
                        <label class="form-label">الصورة الشخصية</label>
                        <div class="image-preview" onclick="document.getElementById('profileImage').click()">
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>اختر صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة الحرفي">
                        </div>
                        <input type="file" class="form-control d-none" id="profileImage" name="profile_image"
                            accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">صورة شخصية (اختياري)</small>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-warning"></i> الاسم الكامل <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="مثال: أحمد النجار" value="{{ old('name') }}" required>
                            </div>

                            <!-- الحرفة -->
                            <div class="col-md-6">
                                <label for="craft_name" class="form-label">
                                    <i class="fas fa-tools text-warning"></i> الحرفة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="craft_name" name="craft_name"
                                    placeholder="مثال: نجارة" value="{{ old('craft_name') }}" required>
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-warning"></i> البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="example@email.com" value="{{ old('email') }}">
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-warning"></i> رقم الهاتف
                                </label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    placeholder="0599 123 456" value="{{ old('phone') }}">
                            </div>

                            <!-- كلمة المرور (جديد) -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-warning"></i> كلمة المرور
                                </label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="********" value="{{ old('password') }}">
                            </div>

                            <!-- سنوات الخبرة -->
                            <div class="col-md-4">
                                <label for="experience_years" class="form-label">
                                    <i class="fas fa-star text-warning"></i> سنوات الخبرة
                                </label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years"
                                    placeholder="8" min="0" value="{{ old('experience_years', 0) }}">
                            </div>

                            <!-- السعر بالساعة -->
                            <div class="col-md-4">
                                <label for="hourly_rate" class="form-label">
                                    <i class="fas fa-clock text-warning"></i> السعر بالساعة (₪)
                                </label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"
                                    placeholder="50" min="0" step="0.5" value="{{ old('hourly_rate', 0) }}">
                            </div>

                            <!-- السعر باليوم -->
                            <div class="col-md-4">
                                <label for="daily_rate" class="form-label">
                                    <i class="fas fa-calendar-day text-warning"></i> السعر باليوم (₪)
                                </label>
                                <input type="number" class="form-control" id="daily_rate" name="daily_rate"
                                    placeholder="350" min="0" step="0.5" value="{{ old('daily_rate', 0) }}">
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن الحرفي وخبراته ومهاراته...">{{ old('description') }}</textarea>
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-6">
                                <label class="form-check-label" for="is_active"> حالة الحساب : </label>
                                <select name="is_active" id="is_active" class="form-control select2"
                                    style="width:100%;">
                                    <option value="1">فعال</option>
                                    <option value="0">غير فعال</option>
                                </select>
                                <span class="text-muted small d-block">إذا كان مفعلاً، سيظهر الحرفي في نتائج
                                    البحث</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning btn-submit">
                        <i class="fas fa-save"></i> حفظ وإضافة
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-back">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                    <a href="{{ route('craftmen.index') }}" class="btn btn-outline-danger btn-back ms-auto">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function performStore() {
    let formData = new FormData();
    // إضافة كل الحقول المطلوبة
    formData.append('name', document.getElementById('name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('phone', document.getElementById('phone').value);
    formData.append('craft_name', document.getElementById('craft_name').value);
    formData.append('experience_years', document.getElementById('experience_years').value);
    formData.append('hourly_rate', document.getElementById('hourly_rate').value);
    formData.append('daily_rate', document.getElementById('daily_rate').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('is_active', document.getElementById('is_active').value);
    formData.append('password', document.getElementById('password').value); // جديد

    // استدعاء الدالة store لإرسال البيانات
    store('/cms/admin/craftmen', formData);

    // function performStore() {
    //     const form = document.getElementById('craftmen-form');
    //     let formData = new FormData(form);
    //     store('/cms/admin/craftmen', formData);
    // }
}
</script>
@endsection