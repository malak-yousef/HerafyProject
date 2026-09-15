@extends('cms.parent')

@section('title', 'عرض بيانات مشرفين')

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
    .form-card .form-select {
        border-radius: 10px;
        border: 1px solid #e4e7eb;
        padding: 10px 14px;
        font-size: 13px;
    }

    .form-card .form-control:hover,
    .form-card .form-select:hover {
        border-color: #f4b400;
    }


    .btn-custom {
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: .3s;
    }

    .btn-custom:hover {
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
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .image-preview i {
        color: #adb5bd;
    }


    .alert {
        border-radius: 10px;
    }
</style>

@endsection

@section('main_content')

عرض بيانات مشرفين

@endsection




@section('content')
<div class="row justify-content-center">

    <div class="col-lg-10">

        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">

                <div>

                    <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                        <i class="fas fa-user-shield" style="color:#f1c40f;"></i>

                        عرض بيانات المشرف

                    </h4>

                </div>


                <a href="{{ route('admins.index') }}"
                    class="btn btn-outline-secondary btn-custom">

                    <i class="fas fa-arrow-right"></i>
                    عودة

                </a>


            </div>

            <div class="row g-3">


                <!-- الصورة -->

                <div class="col-12 mb-4">

                    <label class="form-label d-block text-center">
                        الصورة الشخصية
                    </label>


                    <div class="d-flex justify-content-center">
                        <div class="image-preview">

                            @if($admins->image)

                            <img src="{{ asset('storage/images/admin/'.$admins->image) }}"
                                alt="admin">

                            @else

                            <i class="fas fa-user fa-3x text-secondary"></i>

                            @endif

                        </div>

                    </div>


                </div>





                <!-- الاسم -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-user" style="color:#f1c40f;"></i>
                        الاسم

                    </label>


                    <input disabled readonly
                        class="form-control"
                        value="{{ $admins->name }}">


                </div>





                <!-- اسم المستخدم -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-user-tag" style="color:#f1c40f;"></i>
                        اسم المستخدم

                    </label>


                    <input disabled readonly
                        class="form-control"
                        value="{{ $admins->user_name }}">


                </div>





                <!-- الهاتف -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-phone" style="color:#f1c40f;"></i>
                        رقم الهاتف

                    </label>


                    <input disabled readonly
                        class="form-control"
                        value="{{ $admins->mobile }}">


                </div>





                <!-- البريد -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-envelope" style="color:#f1c40f;"></i>
                        البريد الإلكتروني

                    </label>


                    <input disabled readonly
                        class="form-control"
                        value="{{ $admins->email }}">


                </div>





                <!-- العنوان -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-map-marker-alt" style="color:#f1c40f;"></i>
                        العنوان

                    </label>


                    <input disabled readonly
                        class="form-control"
                        value="{{ $admins->address }}">


                </div>





                <!-- تاريخ الميلاد -->

                <div class="col-md-6">

                    <label class="form-label">

                        <i class="fas fa-calendar" style="color:#f1c40f;"></i>
                        تاريخ الميلاد

                    </label>


                    <input disabled readonly
                        type="date"
                        class="form-control"
                        value="{{ $admins->date }}">


                </div>






                <div class="col-md-6">
                    <label class="form-label" for="status"><i class="fas fa-toggle-on" style="color:#f1c40f;"></i>
                        حالة الحساب</label>
                    <select disabled id="status" name="status" class="form-select">
                        <option value="active" {{ old('status', $admins->status) == 'active' ? 'selected' : '' }}>فعال</option>
                        <option value="inactive" {{ old('status', $admins->status) == 'inactive' ? 'selected' : '' }}>غير فعال</option>
                        <option value="pending" {{ old('status', $admins->status) == 'pending' ? 'selected' : '' }}>قيد التنفيذ</option>
                        <option value="blocked" {{ old('status', $admins->status) == 'blocked' ? 'selected' : '' }}>محظور</option>
                    </select>
                </div>


                <div class="col-md-6">

                    <label class="form-label" for="gender"><i class="fas fa-venus-mars" style="color:#f1c40f;"></i>
                        الجنس</label>
                    <select disabled id="gender" name="gender" class="form-select">
                        <option value="male" {{ old('gender', $admins->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $admins->gender) == 'female' ? 'selected' : '' }}>انثى</option>
                    </select>

                </div>







                <!-- الأزرار -->

                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">


                    <a href="{{route('admins.edit',$admins->id)}}"
                        class="btn btn-warning btn-custom">

                        <i class="fas fa-edit"></i>
                        تعديل

                    </a>


                    <a href="{{route('admins.index')}}"
                        class="btn btn-outline-danger btn-custom">

                        <i class="fas fa-times"></i>
                        إلغاء

                    </a>



                </div>



            </div>

        </div>

    </div>


    @endsection
