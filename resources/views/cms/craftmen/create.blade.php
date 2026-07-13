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
                        <div class="image-preview" onclick="document.getElementById('image').click()">
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>اختر صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة الحرفي">
                        </div>
                        <input type="file" class="form-control d-none" id="image" name="image"
                            accept="image/*">
                        <small class="text-muted">صورة شخصية (اختياري)</small>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user text-warning"></i> الاسم الاول <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="first_name" first_name="first_name"
                                    placeholder="مثال: أحمد " value="{{ old('first_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user text-warning"></i> الاسم الاخير <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="last_name" last_name="last_name"
                                    placeholder="مثال: محمد" value="{{ old('last_name') }}" required>
                            </div>

                            <!-- رقم الهوية -->
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">
                                    <i class="fas fa-id-card text-warning"></i> رقم الهوية <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="id_number" name="id_number"
                                    placeholder="مثال: 123456789" value="{{ old('id_number') }}" required>
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
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-mobile text-warning"></i> رقم الهاتف
                                </label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="0599 123 456" value="{{ old('mobile') }}">
                            </div>

                            <!-- كلمة المرور (جديد) -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-warning"></i> كلمة المرور
                                </label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="********" value="{{ old('password') }}">
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-6">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> العنوان
                                </label>                                
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="مثال: شارع فلسطين، غزة" value="{{ old('address') }}">
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

                            <!-- الجنس -->
                            <div class="col-6">
                                <label class="form-check-label" for="gender"> الجنس : </label>
                                <select name="gender" id="gender" class="form-control select2" style="width:100%;">
                                    <option value="male">ذكر</option>
                                    <option value="female">انثى</option>
                                </select>
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-6">
                                <label class="form-check-label" for="status"> حالة الحساب : </label>
                                <select name="status" id="status" class="form-control select2"
                                    style="width:100%;">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="active">فعال</option>
                                    <option value="inactive">غير فعال</option>
                                    <option value="blocked">محظور</option>
                                </select>
                                <span class="text-muted small d-block">إذا كان مفعلاً، سيظهر الحرفي في نتائج
                                    البحث</span>
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن الحرفي وخبراته ومهاراته...">{{ old('description') }}</textarea>
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
        formData.append('first_name', document.getElementById('first_name').value);
        formData.append('last_name', document.getElementById('last_name').value);
        formData.append('id_number', document.getElementById('id_number').value);
        formData.append('mobile', document.getElementById('mobile').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('craft_name', document.getElementById('craft_name').value);
        formData.append('experience_years', document.getElementById('experience_years').value);
        formData.append('hourly_rate', document.getElementById('hourly_rate').value);
        formData.append('daily_rate', document.getElementById('daily_rate').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('image', document.getElementById('image').files[0]);

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