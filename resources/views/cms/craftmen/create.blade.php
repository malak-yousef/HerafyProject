@extends('cms.parent')

@section('title', 'اضافة حرفي')


@section('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #f1f2f6;
        box-shadow: 0 4px 24px rgba(0, 0, 0, .02);
    }

    .form-card .form-label {
        font-weight: 600;
        color: #495057;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-card .form-control,
    .form-card .form-select {
        border-radius: 10px;
        border: 1px solid #e4e7eb;
        padding: 10px 14px;
        font-size: 13px;
        transition: .25s;
    }

    .form-card .form-control:focus,
    .form-card .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
    }

    textarea.form-control {
        resize: none;
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
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
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
        border: 3px dashed #D4AF37;
        border-radius: 50%;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
        transition: .25s;
    }

    .image-preview:hover {
        border-color: #f1c40f;
        background: #fcfdfe;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }



    }

    .btn-custom {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        transition: .25s;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .alert {
        border-radius: 10px;
        border: none;
        font-size: 13px;
    }

    .btn-back {
        min-width: 110px;
    }

    .form-control:hover,
    .form-select:hover {
        border-color: #f4b400;
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
                    <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                        <i class="fas fa-user-plus" style="color: #f1c40f;"></i>
                        إضافة حرفي جديد
                    </h4>
                    <p class="text-muted small mb-0">أدخل بيانات الحرفي الجديد لإضافته إلى المنصة</p>
                </div>
                <a href="{{ route('craftmen.index') }}" class="btn btn-outline-secondary btn-back btn-custom">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عنصر عرض الأخطاء (جديد) -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="craftmen-form" action="#" onsubmit="event.preventDefault(); performStore();">
                <div class="row">

                    <!-- الصورة الشخصية -->
                    <div class="col-12 mb-4">

                        <label class="form-label d-block text-center">
                            الصورة الشخصية
                        </label>

                        <div class="d-flex justify-content-center">

                            <div class="image-preview"
                                onclick="document.getElementById('image').click()">

                                <div class="placeholder" id="imagePlaceholder">
                                    <i class="fas fa-camera" style="color: #f1c40f;"></i>
                                    <span>اختر صورة</span>
                                </div>

                                <img id="imagePreview"
                                    style="display:none;"
                                    alt="صورة الحرفي">

                            </div>

                        </div>

                        <input
                            type="file"
                            class="form-control d-none"
                            id="image"
                            name="image"
                            accept="image/*">

                        <small class="text-muted d-block text-center mt-2">
                            صورة شخصية (اختياري)
                        </small>

                    </div>

                    <div class="col-12">
                        <div class="row g-3">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user" style="color: #f1c40f;"></i> الاسم الكامل <span
                                        class="text-danger fw-bold">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="مثال: أحمد " value="{{ old('name') }}" required>
                            </div>

                            <!-- اسم المستخدم -->
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">
                                    <i class="fas fa-user" style="color: #f1c40f;"></i> اسم المستخدم <span
                                        class="text-danger fw-bold">*</span>
                                </label>
                                <input type="text" class="form-control" id="user_name" name="user_name"
                                    placeholder="مثال: abdMash1" value="{{ old('user_name') }}" required>
                            </div>

                            <!-- رقم الهوية -->
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">
                                    <i class="fas fa-id-card" style="color: #f1c40f;"></i> رقم الهوية <span
                                        class="text-danger fw-bold">*</span>
                                </label>
                                <input type="text" class="form-control" id="id_number" name="id_number"
                                    placeholder="مثال: 123456789" value="{{ old('id_number') }}" required>
                            </div>


                            <div class="col-md-6 mb-4">

                                <label class="form-label" for="profession_id">
                                    اسم الحرفة
                                </label>

                                <select class="form-select" id="profession_id" name="profession_id">
                                    <option value="">اختر الحرفة</option>
                                    @foreach($professions as $profession)
                                    <option value="{{ $profession->id }}">{{ $profession->name }}</option>
                                    @endforeach

                                </select>

                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope" style="color: #f1c40f;"></i> البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="example@email.com" value="{{ old('email') }}">
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-mobile" style="color: #f1c40f;"></i> رقم الهاتف
                                </label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="0599 123 456" value="{{ old('mobile') }}">
                            </div>

                            <!-- كلمة المرور  -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock" style="color: #f1c40f;"></i> كلمة المرور
                                </label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="********" value="{{ old('password') }}">
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-6">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt" style="color: #f1c40f;"></i> العنوان
                                </label>
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="مثال: شارع فلسطين، غزة" value="{{ old('address') }}">
                            </div>

                            <!-- سنوات الخبرة -->
                            <div class="col-md-4">
                                <label for="experience_years" class="form-label">
                                    <i class="fas fa-star" style="color: #f1c40f;"></i> سنوات الخبرة
                                </label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years"
                                    placeholder="8" min="0" value="{{ old('experience_years', 0) }}">
                            </div>

                            <!-- السعر بالساعة -->
                            <div class="col-md-4">
                                <label for="hourly_rate" class="form-label">
                                    <i class="fas fa-clock" style="color: #f1c40f;"></i> السعر بالساعة (₪)
                                </label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"
                                    placeholder="50" min="0" step="0.5" value="{{ old('hourly_rate', 0) }}">
                            </div>

                            <!-- السعر باليوم -->
                            <div class="col-md-4">
                                <label for="daily_rate" class="form-label">
                                    <i class="fas fa-calendar-day" style="color: #f1c40f;"></i> السعر باليوم (₪)
                                </label>
                                <input type="number" class="form-control" id="daily_rate" name="daily_rate"
                                    placeholder="350" min="0" step="0.5" value="{{ old('daily_rate', 0) }}">
                            </div>

                            <!-- الجنس -->
                            <div class="col-6">
                                <label class="form-label" for="gender"> الجنس : </label>
                                <select name="gender" id="gender" class="form-select" style="width:100%;">
                                    <option value="male">ذكر</option>
                                    <option value="female">انثى</option>
                                </select>
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-6">
                                <label class="form-label" for="status"> حالة الحساب : </label>
                                <select name="status" id="status" class="form-select"
                                    style="width:100%;">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="active">فعال</option>
                                    <option value="inactive">غير فعال</option>
                                    <option value="blocked">محظور</option>
                                </select>

                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left" style="color: #f1c40f;"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن الحرفي وخبراته ومهاراته...">{{ old('description') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <button type="reset" class="btn btn-outline-secondary btn-custom">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>

                    <button type="submit" class="btn btn-warning btn-submit btn-custom">
                        <i class="fas fa-save"></i> حفظ وإضافة
                    </button>

                    <a href="{{ route('craftmen.index') }}" class="btn btn-outline-danger  btn-custom">
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
        formData.append('user_name', document.getElementById('user_name').value);
        formData.append('mobile', document.getElementById('mobile').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('gender', document.getElementById('gender').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('id_number', document.getElementById('id_number').value);
        // formData.append('craft_name', document.getElementById('craft_name').value);
        formData.append('profession_id', document.getElementById('profession_id').value);
        formData.append('experience_years', document.getElementById('experience_years').value);
        formData.append('hourly_rate', document.getElementById('hourly_rate').value);
        formData.append('daily_rate', document.getElementById('daily_rate').value);
        formData.append('description', document.getElementById('description').value);
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