@extends('cms.parent')

@section('title', 'اضافة مشرف')

@section('styles')
<style>
.admins-page{
    padding:30px;
}

.admin-card{
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 5px 25px #00000010;
}

.add-btn{
    background:#ffbf00;
    color:#14213d;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-weight:bold;
}

/* تحسينات إضافية لتناسق الحقول */
.form-group {
    margin-bottom: 15px;
}
.form-label {
    font-weight: 600;
    color: #14213d;
    margin-bottom: 8px;
    display: block;
}
.form-control, .form-select {
    height: 45px;
    border-radius: 10px;
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
            <div class="card invoice-container">
                
                <form id="admin-form" onsubmit="event.preventDefault();">
                    
                    <div class="card-header">
                        <h5>اضافة مشرف</h5>
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">

                                <div class="d-flex d-md-none">
                                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                        <i class="feather-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </a>
                                </div>

                                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                                    <button type="button" onclick="performStore()" class="btn btn-primary">store</button>
                                    <a href="{{ route('admins.index') }}" type="submit"  class="btn btn-light-brand">
                                        <i class="feather-rotate-ccw me-2"></i>
                                        <span>Go Back</span>
                                    </a>
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
                                    <label class="form-label" for="first_name">الاسم الاول</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="الاسم الاول">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="last_name">الاسم الاخير</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="الاسم الاخير">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">البريد الالكتروني</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="البريد الالكتروني">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="mobile">رقم الهاتف</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="رقم الهاتف">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="address">العنوان</label>
                                    <input type="text" id="address" name="address" class="form-control" placeholder="العنوان">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="date">تاريخ الميلاد</label>
                                    <input type="date" id="date" name="date" class="form-control">
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
                                    <label class="form-label" for="image">الصورة</label>
                                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
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

                        </div> </div> </form>
             </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function performStore(){

    let formData = new FormData();

    formData.append('first_name', document.getElementById('first_name').value);
    formData.append('last_name', document.getElementById('last_name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('mobile', document.getElementById('mobile').value);
    formData.append('address', document.getElementById('address').value);
    formData.append('date', document.getElementById('date').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('status', document.getElementById('status').value);
    formData.append('gender', document.getElementById('gender').value);
    
    
    
       
      if(document.getElementById('image').files[0]){
    formData.append('image', document.getElementById('image').files[0]);
}

    store('/cms/admin/admins', formData);
}
</script>
@endsection
{{--اضافة هذا الجزء الى البيرنت
 <li class="nav-item">
                <a class="nav-link" href="#" onclick="toggleMenu(event, this, 'adminsMenu')">
                    <i class="fas fa-tools"></i> المشرفين
                    <i class="fas fa-angle-left arrow"></i>
                </a>
                <ul class="submenu" id="adminsMenu" style="display:none;">
                    <li><a href="{{ route('admins.index') }}"><i class="fas fa-user-shield"></i> عرض المشرفين</a></li>
                    <li><a href="{{ route('admins.create') }}"><i class="fas fa-user-plus"></i> إضافة مشرف</a></li>
                </ul>
            </li> --}}