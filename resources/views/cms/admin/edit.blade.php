@extends('cms.parent')

@section('title', 'تعديل مشرفين')

@section('styles' )

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
    .form-card .form-control {
        border-radius: 10px;
        border: 1px solid #e4e7eb;
        padding: 10px 14px;
        font-size: 13px;
        transition: .25s;
    }

    .form-card .form-control:focus,
    .form-card .form-control:focus {
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
    .form-control:hover {
        border-color: #f4b400;
    }
</style>

@endsection

@section('main_content' , 'تعديل مشرفين')


@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                        <i class="fas fa-user-edit" style="color:#f1c40f;"></i>
                        تعديل بيانات المشرفين
                    </h4>

                    <p class="text-muted small mb-0">
                        يمكنك تعديل بيانات المشرف ثم حفظ التغييرات
                    </p>
                </div>


                <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
            <!-- عنصر عرض الأخطاء (جديد) -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="editAdminsForm" action="#"
                onsubmit="event.preventDefault(); performUpdate({{$admins->id}});">

                <div class="row">

                    <!-- الصورة -->

                    <div class="col-12 mb-4">

                        <label class="form-label d-block text-center">
                            الصورة الشخصية
                        </label>


                        <div class="d-flex justify-content-center">
                            <div class="image-preview" onclick="document.getElementById('image').click()">

                                @if($admins->image)

                                <img id="img-preview" src="{{ asset('storage/images/admin/'.$admins->image) }}"
                                    alt="صورة المشرف" style="width:100%; height:100%; object-fit:cover;">

                                @else
                                <div class="placeholder" id="imagePlaceholder">
                                    <i class="fas fa-camera" style="color: #f1c40f;"></i>
                                    <span>عدل صورة</span>
                                </div>
                                <img id="imagePreview" style="display: none;" alt="صورة المشرف">
                                @endif
                            </div>
                            <input type="file" class="form-control d-none" id="image" name="image" accept="image/*">
                            <small class="text-muted d-block text-center mt-2">صورة شخصية (اختياري)</small>
                        </div>
                    </div>







                    <div class="card-body p-4">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name"><i class="fas fa-user" style="color:#f1c40f"></i>ادخل الاسم</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="تعديل الاسم الأول..." value="{{ $admins->name }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="user_name"><i class="fas fa-user" style="color: #f1c40f;"></i>اسم المستخدم</label>
                                    <input type="text" id="user_name" name="user_name" class="form-control" placeholder="تعديل  اسم المستخدم..." value="{{ $admins->user_name }}">
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="mobile"> <i class="fas fa-mobile-alt" style="color:#f1c40f"></i>
                                        رقم الهاتف</label>

                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="تعديل الموبايل..." value="{{ $admins->mobile }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email"><i class="fas fa-envelope" style="color:#f1c40f"></i>
                                        البريد الإلكتروني</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="تعديل البريد..." value="{{ $admins->email }}">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="password"><i class="fas fa-lock" style="color:#f1c40f"></i>
                                        كلمة المرور</label>
                                    <input type="password" id="password" name="password" class="form-control" value="{{ $admins->password }}" disabled readonly>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="password"> <i class="fas fa-lock" style="color:#f1c40f"></i>
                                        كلمة المرور </label>
                                    <input type="password" id="password" name="password" class="form-control" value="{{ $admins->password }}" disabled readonly>
                                </div>
                            </div>





                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="address">العنوان</label>
                                    <input type="text" id="address" name="address" class="form-control" placeholder="العنوان.." value="{{ $admins->address }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="date">ادخل تاريخ الميلاد</label>
                                    <input type="date" id="date" name="date" class="form-control" value="{{ $admins->date }}">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="status">الحالة</label>
                                    <select id="status" name="status" class="form-select">
                                        <option selected hidden>{{ $admins->status }}</option>
                                        <option value="active">فعال</option>
                                        <option value="in_active">غير فعال</option>
                                        <option value="pending">قيد التنفيذ</option>
                                        <option value="blocked">محظور</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <option selected hidden>{{ $admins->gender }}</option>

                                    <label class="form-label" for="gender">الجنس</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="male">ذكر</option>
                                        <option value="female">انثى</option>
                                    </select>
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
                            <i class="fas fa-save"></i> حفظ التعديل
                        </button>

                        <a href="{{ route('admins.index') }}" class="btn btn-outline-danger btn-custom">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
            </form>

        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')



<script>
    function performStore() {
        const form = document.getElementById('admin-form');
        let formData = new FormData(form);

        // إرسال البيانات
        // store('/cms/admin/deanships', formData , 'deanship-form');
        store('/cms/admin/admins', formData);
    }
</script>

<script>
    function performUpdate(id) {

        let formData = new FormData();

        formData.append('name', document.getElementById('name').value);
        formData.append('user_name', document.getElementById('user_name').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('mobile', document.getElementById('mobile').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('date', document.getElementById('date').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('gender', document.getElementById('gender').value);
        if (document.getElementById('image').files[0]) {
            formData.append('image', document.getElementById('image').files[0]);
        }

        // إرسال البيانات 
        storeRoute('/cms/admin/admins_update/' + id, formData);
    }
</script>


@endsection