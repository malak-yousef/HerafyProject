@extends('cms.parent')

@section('title', 'عرض مهن')

@section('styles' )

<style>

.profession-page{
    padding:30px;
}

.profession-card{
    background:#fff;
    border-radius:18px;
    padding:30px;
    box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.page-title{
    color:#14213d;
    font-weight:bold;
    margin-bottom:25px;
}

.form-label{
    font-weight:600;
    color:#14213d;
    margin-bottom:8px;
}

.form-control,
.form-select{
    border-radius:12px;
    border:1px solid #ddd;
    padding:12px;
}

.form-control:focus,
.form-select:focus{
    border-color:#ffbf00;
    box-shadow:0 0 0 .2rem rgba(255,191,0,.25);
}

textarea{
    resize:none;
}

.save-btn{
    background:#ffbf00;
    color:#14213d;
    border:none;
    padding:12px 30px;
    border-radius:12px;
    font-weight:bold;
    transition:.3s;
}

.save-btn:hover{
    background:#e6aa00;
}

.cancel-btn{
    background:#14213d;
    color:white;
    border:none;
    padding:12px 30px;
    border-radius:12px;
    margin-left:10px;
    transition:.3s;
}

.cancel-btn:hover{
    background:#0d1726;
}

</style>


@endsection


@section('main_content')

عرض مهن

@endsection

@section('content')
<div class="container profession-page">

    <div class="profession-card">

        <h3 class="page-title">
            عرض مهنة جديدة
        </h3>

        <form>



         <!-- ID -->
    <div class="col-md-4 mb-4">
        <label class="form-label">ID</label>
        <input
            type="text"
            class="form-control"
            value="{{ $professions->id }}"
            disabled>
    </div>

            <div class="row">

                <!-- اسم المهنة -->
                <div class="col-md-6 mb-4">
                    <label class="form-label" for="name">
                        اسم المهنة
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control"
                        placeholder="أدخل اسم المهنة"
                         value="{{ $professions->name }}"
                         disabled>
                        
                </div>
{{-- <select class="form-select" id="category_id" name="category_id" disabled>

@foreach($categories as $category)

<option
    value="{{ $category->id }}"
    @if($category->id == $professions->category_id) selected @endif>
    {{ $category->name }}
</option>

@endforeach

</select> --}}
                <!-- التصنيف -->
                <div class="col-md-6 mb-4">

                    <label class="form-label" for="category_id">
                        اسم التصنيف
                    </label>

                    <select class="form-select" id="category_id" name="category_id" disabled>
            {{--  <option selected  value=""> {{ $professions->category->name ?? "" }}</option> --}}      
                       {{--  <option>النجارة</option>
                        <option>السباكة</option>
                        <option>الحدادة</option>
                        <option>الكهرباء</option> --}}
                       @foreach($categories as $category)
                     
                   <option   value="{{ $category->id }}">{{ $category->name }} </option> 
               {{--  <option @if ($category->id == $professions->category_id) selected @endif value="{{ $category->id ?? "" }}">
                 {{ $category->name ?? "" }}></option> --}}
                 
                       @endforeach

                    </select>

                </div>

            </div>

            <!-- الوصف -->
            <div class="mb-4">

                <label class="form-label" for="description">
                    الوصف
                </label>

                <textarea
                id="description"
                name="description"
                    rows="5"
                    class="form-control"
                    placeholder="اكتب وصفاً للمهنة"
                    disabled>{{ old('description', $professions->description) }}</textarea>

            </div>

            <!-- الحالة -->
            <div class="mb-4">
                 <label class="form-label" for="status" >
                    الحالة
                </label>
                <select id="status" name="status" class="form-select" disabled >
    <option value="active" 
       {{ old('status', $professions->status) == 'active' ? 'selected' : '' }}>فعال</option>
    <option value="in_active" 
      {{ old('status', $professions->status) == 'in_active' ? 'selected' : '' }}>غير فعال</option>
</select>
            </div>


         


 <!-- تاريخ الإنشاء -->
    <div class="col-md-4 mb-4">
        <label class="form-label">تاريخ الإنشاء</label>
        <input
            type="text"
            class="form-control"
            value="{{ $professions->created_at->format('Y-m-d H:i') }}"
            disabled>
    </div>

    <!-- تاريخ آخر تعديل -->
    <div class="col-md-4 mb-4">
        <label class="form-label">آخر تعديل</label>
        <input
            type="text"
            class="form-control"
            value="{{ $professions->updated_at->format('Y-m-d H:i') }}"
            disabled>
    </div>




  {{--  <button type="button" onclick="performUpdate({{ $professions->id }})" class="btn save-btn ">Update</button>--}}

           <a href="{{ route('professions.index') }}" class="btn cancel-btn">
                                        <i class="feather-rotate-ccw me-2"></i>
                                        <span>Go Back</span>
                                    </a>


        </form>

    </div>

</div>
@endsection

@section('scripts')

{{--  --}}
<script>
    function performUpdate(id){
        let formData = new FormData();
        formData.append('name',document.getElementById('name').value);
        formData.append('description',document.getElementById('description').value);
        formData.append('status',document.getElementById('status').value);
        formData.append('category_id',document.getElementById('category_id').value);
        // إرسال البيانات
        storeRoute('/cms/admin/professions_update/'+id , formData );
    }
</script> 
@endsection
