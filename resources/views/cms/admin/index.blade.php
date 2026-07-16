@extends('cms.parent')

@section('title', 'المشرفين الرئيسية')


@section('styles')

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

.action-btn {
    width: 38px;
    height: 38px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    margin: 3px;
    text-decoration: none;
    font-size: 17px;
    transition: .3s;
    
}


/* عرض */
.show-btn {
    background: #e8f1ff;
    color: #2563eb;
}


/* تعديل */
.edit-btn {
    background: #fff4dd;
    color: #f59e0b;
}


/* حذف */
.delete-btn {
    background: #ffe5e5;
    color: #dc2626;
    border: none;
}


/* Hover */
.action-btn:hover {
    transform: translateY(-3px);
    opacity: .8;
}

.description-column{
    max-width: 350px;
    white-space: normal;
    word-wrap: break-word;
}

.actions-column{
    white-space: nowrap;
    width: 180px;
}

.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 25px;
}

.pagination {
    list-style: none;
    display: flex;
    gap: 8px;
    padding: 0;
}

.pagination li a {
    text-decoration: none;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #ddd;
    color: #6c63ff;
    background: white;
    font-size: 14px;
}

.pagination li.active a {
    background: #6c63ff;
    color: white;
}

.pagination li a:hover {
    background: #eee;
}

</style>

@endsection



@section('main_content')

المشرفين

@endsection



@section('content')


<div class="container-fluid admin-page">


<div class="card admin-card">


<div class="d-flex justify-content-between mb-4">


<h3>
إدارة المشرفين
</h3>


<a href="{{ route('admins.create') }} ">
    <button class="add-btn"
data-bs-toggle="modal"
data-bs-target="#addadminModal">

+ إضافة مشرف

</button>
</a>


<a href="{{ route('admins_trashed') }} ">
    <button class="btn btn-danger"
data-bs-toggle="modal"
data-bs-target="#addCategoryModal">

سلة المحذوفات 

</button>
</a>

</div>




<table class="table table-hover align-middle">

<thead>

<tr>

<th style="width: 15px">الرقم#</th>
 <th class="text-center">الصورة</th> 
<th class="text-center">الاسم </th>

<th class="text-center">اسم المستخدم</th>

<th class="text-center">البريد الالكتروني</th>

<th class="text-center">الموبايل</th>

<th class="text-center">تاريخ الميلاد</th>

<th class="text-center">الحالة</th>
<th class="text-center">الجنس</th>






{{-- ما بنفع نحطها هان لانها مشفرة
<th class="text-center">كلمة السر</th> --}}




<th class="text-center actions-column">اجراءات</th>

 
</tr>

</thead>



<tbody>


@foreach($admins as $admin)


<tr>

<td>{{ $loop->iteration }}</td>



<td>
    <div class="avatar avatar-md">
   <img src="{{ asset('storage/images/admin/' . $admin->image) }}"
     alt="user-image"
     width="50"
     height="50"
     class="rounded-circle border">
    </div>
</td>
 

<td>
    <span class="badge rounded-pill bg-success">
        {{ $admin->name ?? "" }}
    </span>
</td>

<td>
    <span class="badge rounded-pill bg-success">
        {{ $admin->user_name ?? "" }}
    </span>
</td>

<td>
    <span class="badge rounded-pill bg-info text-dark">
        {{ $admin->email }}
    </span>
</td>

<td>
    <span class="badge rounded-pill bg-dark">
        {{ $admin->mobile ?? "" }}
    </span>
</td>



<td>
    <span class="badge rounded-pill bg-danger">
        {{ $admin->date ?? "" }}
    </span>
</td>

<td>
    @if($admin->status == 'active')
    <span class="badge rounded-pill bg-success">
        {{ $admin->status ?? "" }}
    </span>

@elseif($admin->status == 'inactive')
    <span class="badge rounded-pill bg-secondary">
        {{ $admin->status ?? "" }}
    </span>

@elseif($admin->status == 'pending')
    <span class="badge rounded-pill bg-warning text-dark">
        {{ $admin->status ?? "" }}
    </span>

@elseif($admin->status == 'blocked')
    <span class="badge rounded-pill bg-danger">
        {{ $admin->status ?? "" }}
    </span>

@endif
</td>

<td>
    
    @if($admin->gender == 'male')
        <span class="badge rounded-pill bg-primary">
            {{ $admin->gender ?? "" }}
        </span>
    @else
        <span class="badge rounded-pill bg-warning text-dark">
            {{ $admin->gender ?? "" }}
        </span>
    @endif
</td>
{{--<td>
{{ $admin->image }}
</td>  --}}

{{-- 
ما بنفع نحطها هان لانها مشفرة
<td>
{{ $admin->password }}
</td> --}}






<td class="text-center actions-column">

    <!-- زر العرض Show -->
    <a href="{{ route('admins.show', $admin->id) }}" 
       class="action-btn show-btn"
       title="عرض">
        <i class="fa-solid fa-eye"></i>
    </a>


    <!-- زر التعديل Edit -->
    <a href="{{ route('admins.edit', $admin->id) }}" 
       class="action-btn edit-btn"
       title="تعديل">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>



 <!-- زر الحذف Delete -->
    <form action="{{ route('admins.destroy', $admin->id) }}" 
          method="POST" 
          style="display:inline-block">

        @csrf
        @method('DELETE')

        <button type="button" 
        onclick="performDestroy({{$admin->id}} , this)"
                class="action-btn delete-btn"
                title="نقل لسلة المحذوفات">
            <i class="fa-solid fa-trash"></i>
        </button>



</form>
    

</td>

</tr>


@endforeach



</tbody>


</table>



{{ $admins->links() }}


</div>


</div>



@endsection



@section('scripts')
<script>
    function performDestroy(id , reference){
        confirmDestroy('/cms/admin/admins/'+id , reference);
    }
</script>
@endsection