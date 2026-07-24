@extends('cms.parent')

@section('title', 'عرض بيانات مشرفين')

@section('styles' )

<style>

.admin-page{
    padding:30px;
}

.admin-card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 25px #00000010;
}

.add-btn{

    
        background: #ffbf00;
        color: #14213d;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: bold;

    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-label {
        font-weight: 600;
        color: #14213d;
    }

    .form-control,
    .form-select {
        height: 45px;
        border-radius: 10px;
    }
</style>

@endsection

@section('main_content')

عرض بيانات مشرفين

@endsection




@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-container">

                <form id="admin-form">

                    <div class="card-header d-flex align-items-center">
                        <h5>عرض بيانات مشرف</h5>
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="d-flex d-md-none">
                                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                        <i class="feather-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </a>
                                </div>
                                <div class="buttons">

                                    <a href="{{ route('admins.index') }}" class="btn btn-info">Go Back</a>
                                </div>
                            </div>
                            <div class="d-md-none d-flex align-items-center">
                                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                                    <i class="feather-align-right fs-20"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">ادخل الاسم </label>
                                    <input disabled type="text" id="name" name="name" class="form-control" placeholder="تعديل الاسم ..." value="{{ $admins->name }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="user_name">اسم المستخدم</label>
                                    <input disabled type="text" id="user_name" name="user_name" class="form-control" placeholder="تعديل اسم المستخدم..." value="{{ $admins->user_name}}">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">الصورة</label>

                                    <div>
                                        <img src="{{ asset('storage/images/admin/'.$admins->image) }}"
                                            width="50"
                                            height="50"
                                            style="border-radius:10px; object-fit:contain;">
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="mobile">ادخل الموبايل</label>
                                    <input disabled type="text" id="mobile" name="mobile" class="form-control" placeholder="تعديل الموبايل..." value="{{ $admins->mobile }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">ادخل البريد</label>
                                    <input disabled type="email" id="email" name="email" class="form-control" placeholder="تعديل البريد..." value="{{ $admins->email }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="address">العنوان</label>
                                    <input disabled type="text" id="address" name="address" class="form-control" placeholder="العنوان.." value="{{ $admins->address }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="date">ادخل تاريخ الميلاد</label>
                                    <input disabled type="date" id="date" name="date" class="form-control" value="{{ $admins->date }}">
                                </div>
                            </div>




                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="status">الحالة</label>
                                    <select disabled id="status" name="status" class="form-select">
                                        <option value="active" {{ old('status', $admins->status) == 'active' ? 'selected' : '' }}>فعال</option>
                                        <option value="in_active" {{ old('status', $admins->status) == 'in_active' ? 'selected' : '' }}>غير فعال</option>
                                        <option value="pending" {{ old('status', $admins->status) == 'pending' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="blocked" {{ old('status', $admins->status) == 'blocked' ? 'selected' : '' }}>محظور</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="gender">الجنس</label>
                                    <select disabled id="gender" name="gender" class="form-select">
                                        <option value="male" {{ old('gender', $admins->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $admins->gender) == 'female' ? 'selected' : '' }}>انثى</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')






@endsection