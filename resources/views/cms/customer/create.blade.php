@extends('cms.parent')

@section('title', 'اضافة زبون')

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

@section('main_content', 'اضافة زبون')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-plus text-warning"></i> اضافة زبون
                    </h5>
                    <p class="text-muted small mb-0">أدخل بيانات الزبون الجديد</p>
                </div>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عرض الأخطاء -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="admin-form" onsubmit="event.preventDefault(); performStore();">

                <div class="row g-4">

                    <!-- الصورة الشخصية -->
                    <div class="col-md-3">
                        <label class="form-label">الصورة الشخصية</label>
                        <div class="image-preview" onclick="document.getElementById('image').click()">
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>اختر صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة الزبون">
                        </div>
                        <input type="file" class="form-control d-none" id="image" name="image"
                            accept="image/*">
                        <small class="text-muted">صورة شخصية (اختياري)</small>
                    </div>

                    <!-- باقي الحقول -->
                    <div class="col-md-9">
                        <div class="row g-3">

                            <!-- الاسم -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-warning"></i> الاسم الكامل <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="احمد علي" value="{{ old('name') }}" required>
                            </div>

                            <!-- اليوزر -->
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">
                                    <i class="fas fa-user text-warning"></i> اسم المستخدم <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="user_name" name="user_name" class="form-control"
                                    placeholder="اسم المستخدم" value="{{ old('user_name') }}" required>
                            </div>

                            <!-- البريد الالكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-warning"></i> البريد الالكتروني <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="example@email.com" value="{{ old('email') }}" required>
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

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-phone text-warning"></i> رقم الهاتف
                                </label>
                                <input type="text" id="mobile" name="mobile" class="form-control"
                                    placeholder="0599 123 456" value="{{ old('mobile') }}">
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-6">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> العنوان
                                </label>
                                <input type="text" id="address" name="address" class="form-control"
                                    placeholder="العنوان" value="{{ old('address') }}">
                            </div>

                            <!-- تاريخ الميلاد -->
                            <div class="col-md-6">
                                <label for="date" class="form-label">
                                    <i class="fas fa-calendar text-warning"></i> تاريخ الميلاد
                                </label>
                                <input type="date" id="date" name="date" class="form-control"
                                    value="{{ old('date') }}">
                            </div>

                            <!-- كلمة السر -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock text-warning"></i> كلمة السر <span class="text-danger">*</span>
                                </label>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="كلمة السر" required>
                            </div>

                            <!-- الجنس -->
                            <div class="col-md-6">
                                <label for="gender" class="form-label">
                                    <i class="fas fa-venus-mars text-warning"></i> الجنس
                                </label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">اختر الجنس</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                </select>
                            </div>

                            <!-- الحالة -->
                            <div class="col-md-6">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on text-warning"></i> الحالة
                                </label>
                                <select class="form-select" name="status" id="status">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>فعال</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير فعال</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>محظور</option>
                                </select>
                                <small class="text-muted">حالة حساب الزبون</small>
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
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-danger btn-back ms-auto">
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
    // ============================================
    // معاينة الصورة قبل الرفع
    // ============================================
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('imagePlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }

    // ============================================
    // حفظ البيانات
    // ============================================
    function performStore() {
        let formData = new FormData();

        formData.append('id_number', document.getElementById('id_number').value);
        formData.append('name', document.getElementById('name').value);
        formData.append('user_name', document.getElementById('user_name').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('mobile', document.getElementById('mobile').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('date', document.getElementById('date').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('gender', document.getElementById('gender').value);

        if (document.getElementById('image').files[0]) {
            formData.append('image', document.getElementById('image').files[0]);
        }

        store('/cms/admin/customers', formData);
    }

    // ============================================
    // إعادة تعيين النموذج
    // ============================================
    document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'إعادة تعيين',
            text: 'هل أنت متأكد من إعادة تعيين جميع الحقول؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FFC107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، إعادة تعيين',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('admin-form').reset();
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('imagePlaceholder').style.display = 'block';
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                Swal.fire({
                    icon: 'success',
                    title: '✅ تم',
                    text: 'تم إعادة تعيين النموذج',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });


    console.log('✅ صفحة اضافة زبون جاهزة');
</script>
@endsection