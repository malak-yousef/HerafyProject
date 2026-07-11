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
                                    <label class="form-label" for="first_name">ادخل الاسم الأول</label>
                                    <input disabled type="text" id="first_name" name="first_name" class="form-control" placeholder="تعديل الاسم الأول..." value="{{ $admins->user->first_name }}">
                                </div> 
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="last_name">ادخل الاسم الثاني</label>
                                    <input disabled type="text" id="last_name" name="last_name" class="form-control" placeholder="تعديل الاسم الثاني..." value="{{ $admins->user->last_name }}">
                                </div>
                            </div>
                            {{-- لو خليتها هيك بهذه الواجهة مش حتظهر الصورة
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="image">الصورة</label>
                                    <input disabled type="file" id="image" name="image" class="form-control" accept="image/*">
                                </div>
                            </div> --}}
                             {{--  <div class="col-md-6">
                     <div class="form-group">
                       <label disabled class="form-label">الصورة</label>
                  <div>
        
                <img src="{{ asset('storage/images/admin/'.$admin->user->image) }}"
                     width="150"
                     height="150"
                     style="border-radius:10px; object-fit:contain;">
           
        </div>
    </div>
</div>  --}}
                          
                          <div class="col-md-6">
    <div class="form-group">
        <label class="form-label">الصورة</label>

        <div>
            <img src="{{ asset('storage/images/admin/'.$admins->user->image) }}"
                 width="150"
                 height="150"
                 style="border-radius:10px; object-fit:contain;">
        </div>

    </div>
</div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="mobile">ادخل الموبايل</label>
                                    <input disabled type="text" id="mobile" name="mobile" class="form-control" placeholder="تعديل الموبايل..." value="{{ $admins->user->mobile }}">
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
                                    <input disabled type="text" id="address" name="address" class="form-control" placeholder="العنوان.." value="{{ $admins->user->address }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="date">ادخل تاريخ الميلاد</label>
                                    <input disabled type="date" id="date" name="date" class="form-control" value="{{ $admins->user->date }}">
                                </div>
                            </div>

                            
    

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="status">الحالة</label>
                                    <select disabled id="status" name="status" class="form-select">
                                        <option value="active" {{ old('status', $admins->user->status) == 'active' ? 'selected' : '' }}>فعال</option>
                                        <option value="in_active" {{ old('status', $admins->user->status) == 'in_active' ? 'selected' : '' }}>غير فعال</option>
                                        <option value="pending" {{ old('status', $admins->user->status) == 'pending' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="blocked" {{ old('status', $admins->user->status) == 'blocked' ? 'selected' : '' }}>محظور</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="gender">الجنس</label>
                                    <select disabled id="gender" name="gender" class="form-select">
                                        <option value="male" {{ old('gender', $admins->user->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $admins->user->gender) == 'female' ? 'selected' : '' }}>انثى</option>
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
       
formData.append('first_name', document.getElementById('first_name').value);
formData.append('last_name', document.getElementById('last_name').value);
formData.append('email', document.getElementById('email').value);
formData.append('mobile', document.getElementById('mobile').value);
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