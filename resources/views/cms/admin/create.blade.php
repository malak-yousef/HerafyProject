@extends('cms.parent')

@section('title', 'اضافة مشرف')

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

    .form-control:hover,
    .form-select:hover {
        border-color: #f4b400;
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

    .btn-submit {
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-back {
        min-width: 110px;
    }

    .alert {
        border-radius: 10px;
        border: none;

    }
</style>
@endsection

@section('main_content')
اضافة مشرف
@endsection

@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-xl-12">

            <div class="form-card">



                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                            <i class="fas fa-user-shield" style="color:#f1c40f;"></i>
                            إضافة مشرف جديد
                        </h4>


                        <p class="text-muted small mb-0">
                            أدخل بيانات المشرف الجديد لإضافته إلى النظام
                        </p>

                    </div>

                    <a href="{{ route('admins.index') }}"
                        class="btn btn-outline-secondary btn-back btn-custom">
                        <i class="fas fa-arrow-right"></i>
                        العودة
                    </a>



                </div>


                <!-- عنصر عرض الأخطاء (جديد) -->
                <div id="error_alert" class="alert alert-danger" hidden>
                    <ul id="error_messages_ul"></ul>
                </div>

                <!-- نموذج الإضافة -->
                <form id="craftmen-form" action="#" onsubmit="event.preventDefault(); performStore();">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label d-block text-center">
                                الصورة الشخصية
                            </label>

                            <div class="d-flex justify-content-center">
                                <div class="image-preview"
                                    onclick="document.getElementById('image').click()">

                                    <div id="imagePlaceholder">
                                        <i class="fas fa-camera" style="color:#f1c40f"></i>
                                        <span>اختر صورة</span>
                                    </div>

                                    <img id="imagePreview" style="display:none;">

                                    <input type="file"
                                        class="form-control d-none"
                                        id="image"
                                        name="image"
                                        accept="image/*">
                                </div>
                            </div>

                            <small class="text-muted d-block text-center mt-2">
                                صورة شخصية (اختياري)
                            </small>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="role_name">Assign Roles :</label>
                                <select class="form-control select2" id="role_name" name="role_name"
                                    style="width: 100%;">
                                    @foreach($roles as $role)
                                    <option value="{{$role->name}}">{{$role->name}}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="password">كلمة السر</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="كلمة السر">



                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="password_confirmation">
                                    تأكيد كلمة السر
                                </label>

                                <input type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="تأكيد كلمة السر">
                            </div>
                        </div>




                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="status">الحالة</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="pending">قيد التنفيذ</option>
                                    <option value="active">فعال</option>
                                    <option value="inactive">غير فعال</option>
                                    <option value="blocked">محظور</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="gender">الجنس</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">اختر الجنس</option>
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                            </div>
                        </div>








                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user" style="color:#f1c40f"></i>
                                    الاسم الكامل
                                </label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="الاسم ">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="user_name">
                                    <i class="fas fa-user" style="color: #f1c40f;"></i> اسم المستخدم
                                </label>
                                <input type="text" id="user_name" name="user_name" class="form-control" placeholder="اسم المستخدم">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope" style="color:#f1c40f"></i>
                                    البريد الإلكتروني
                                </label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="البريد الالكتروني">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-mobile-alt" style="color:#f1c40f"></i>
                                    رقم الهاتف
                                </label>
                                <input type="text" id="mobile" name="mobile" class="form-control" placeholder="رقم الهاتف">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="address"> <i class="fas fa-map-marker-alt" style="color: #f1c40f;"></i> العنوان</label>
                                <input type="text" id="address" name="address" class="form-control" placeholder="العنوان">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="date">تاريخ الميلاد</label>
                                <input type="date" id="date" name="date" class="form-control">
                            </div>
                        </div>






                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">

                        <button type="reset"
                            class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-undo"></i>
                            إعادة تعيين
                        </button>

                        <button type="button"
                            onclick="performStore()"
                            class="btn btn-warning btn-submit btn-custom">
                            <i class="fas fa-save"></i>
                            حفظ وإضافة
                        </button>

                        <a href="{{ route('admins.index') }}"
                            class="btn btn-outline-danger btn-custom">
                            <i class="fas fa-times"></i>
                            إلغاء
                        </a>

                    </div>
            </div>
            </form>


        </div>
    </div>
    @endsection

    @section('scripts')
    <script>
        function performStore() {

            let formData = new FormData();

            formData.append('name', document.getElementById('name').value);
            formData.append('user_name', document.getElementById('user_name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('mobile', document.getElementById('mobile').value);
            formData.append('address', document.getElementById('address').value);
            formData.append('date', document.getElementById('date').value);
            formData.append('password', document.getElementById('password').value);
            formData.append(
                'password_confirmation',
                document.getElementById('password_confirmation').value
            );
            formData.append('status', document.getElementById('status').value);
            formData.append('gender', document.getElementById('gender').value);
            formData.append('role_name', document.getElementById('role_name').value);




            if (document.getElementById('image').files[0]) {
                formData.append('image', document.getElementById('image').files[0]);
            }

            store('/cms/admin/admins', formData);
        }
    </script>
    @endsection