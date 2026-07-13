@extends('cms.parent')

@section('title', 'تعديل مشرفين')

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

    background:#ffbf00;
    color:#14213d;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    font-weight:bold;

}
.form-group{
    margin-bottom: 10px;
}

.form-label{
    font-weight:600;
    color:#14213d;
}

.form-control,
.form-select{
    height:45px;
    border-radius:10px;
}


</style>

@endsection

@section('main_content')

تعديل مشرفين

@endsection




@section('content')
<div class="main-content">
    <div class="row">
        <div class="col-xl-12">
            <div class="card invoice-container">
                
                <form id="admin-form">
                    
                    <div class="card-header d-flex align-items-center">
                        <h5>تعديل مشرف</h5>
                        <div class="page-header-right ms-auto">
                            <div class="page-header-right-items">
                                <div class="d-flex d-md-none">
                                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                        <i class="feather-arrow-left me-2"></i>
                                        <span>Back</span>
                                    </a>
                                </div>
                                <div class="buttons">
                                    <button class="btn btn-warning" type="button" onclick="performUpdate({{ $admins->id }})">Update</button>
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
                                    <input type="text" id="name" name="name" class="form-control" placeholder="تعديل الاسم الأول..." value="{{ $admins->name }}">
                                </div> 
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="user_name">اسم المستخدم</label>
                                    <input type="text" id="user_name" name="user_name" class="form-control" placeholder="تعديل الاسم الثاني..." value="{{ $admins->user_name }}">
                                </div>
                            </div>
                            
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="image">الصورة</label>
                                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                    <div class="mt-3">
    <img src="{{ asset('storage/images/admin/'.$admins->image) }}"
         width="120"
         height="120"
         style="border-radius:10px;object-fit:contain;">
</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="mobile">ادخل الموبايل</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control" placeholder="تعديل الموبايل..." value="{{ $admins->mobile }}">
                                </div> 
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="email">ادخل البريد</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="تعديل البريد..." value="{{ $admins->email }}">
                                </div>
                            </div>
                             <!-- #region-->

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="password">كلمة السر</label>
                                    <input type="password" id="password" name="password" class="form-control"  value="{{ $admins->password }}" disabled readonly >
                                </div> 
                            </div>



                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="password"> تاكيد كلمة السر </label>
                                    <input type="password" id="password" name="password" class="form-control"  value="{{ $admins->password }}" disabled readonly>
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

                            
        <div class="col-md-9">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="image">ادخل صورة</label>
                        <input id="image" name="image" class="form-control" placeholder=" تعديل صورة..." value="{{ $admins->image }}">
                    </div>
                </div>
            </div>
        </div>
 
         

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="status">الحالة</label>
                                    <select id="status" name="status" class="form-select">
                                        <option selected hidden>{{  $admins->status }}</option>
                                        <option value="active" >فعال</option>
                                        <option value="in_active" >غير فعال</option>
                                        <option value="pending">قيد التنفيذ</option>
                                        <option value="blocked" >محظور</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <option selected hidden>{{  $admins->gender }}</option>
                                    <label class="form-label" for="gender">الجنس</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="male">ذكر</option>
                                        <option value="female" >انثى</option>
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
    function performStore() {
        const form = document.getElementById('admin-form');
        let formData = new FormData(form);

        // إرسال البيانات
        // store('/cms/admin/deanships', formData , 'deanship-form');
        store('/cms/admin/admins', formData);
    }
</script>
<script>
    function performUpdate(id){
        
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

        if(document.getElementById('image').files[0]){
    formData.append('image', document.getElementById('image').files[0]);
}
    
        // إرسال البيانات 
        storeRoute('/cms/admin/admins_update/'+id , formData);
    }
</script>

@endsection