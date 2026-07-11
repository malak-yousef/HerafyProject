@extends('cms.parent')

@section('title', 'التصنيفات الرئيسية')


@section('styles')

<style>

.categories-page{
    padding:30px;
}

.category-card{
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

التصنيفات

@endsection



@section('content')


<div class="container-fluid categories-page">


<div class="card category-card">


<div class="d-flex justify-content-between mb-4">


<h3>
إدارة التصنيفات
</h3>




<a href="{{ route('categories.create') }} ">
    <button class="add-btn"
data-bs-toggle="modal"
data-bs-target="#addCategoryModal">

+ إضافة تصنيف

</button>
</a>

<a href="{{ route('categories_trashed') }} ">
    <button class="btn btn-danger"
data-bs-toggle="modal"
data-bs-target="#addCategoryModal">

سلة المحذوفات للتصنيفات

</button>
</a>

</div>



<table class="table">


<thead>

<tr>

<th style="width: 15px">#</th>

<th class="text-center">اسم التصنيف</th>

<th class="text-center">الوصف</th>

<th class="text-center">عدد المهن المرتبطة بالتصنيف</th>



<th class="text-center">تاريخ الإضافة</th>

<th class="text-center">آخر تعديل</th>

<th class="text-center actions-column">عمليات</th>




</tr>

</thead>



<tbody>


@foreach($categories as $category)


<tr>

<td>
{{ $category->id }}
</td>


<td>
{{ $category->name }}
</td>


<td class="description-column">
    {{ $category->description }}
</td>

<td><span class="badge bg-info">
    {{ $category->professions_count }}
</td>

<td>
{{ $category->created_at->format('Y-m-d') }}
</td>


<td>
{{ $category->updated_at->format('Y-m-d') }}
</td>

<td class="text-center actions-column">

    <!-- زر العرض Show -->
    <a href="{{ route('categories.show', $category->id) }}" 
       class="action-btn show-btn"
       title="عرض">
        <i class="fa-solid fa-eye"></i>
    </a>


    <!-- زر التعديل Edit -->
    <a href="{{ route('categories.edit', $category->id) }}" 
       class="action-btn edit-btn"
       title="تعديل">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>


    <!-- زر الحذف Delete -->
    <form action="{{ route('categories.destroy', $category->id) }}" 
          method="POST" 
          style="display:inline-block">

        @csrf
        @method('DELETE')

        <button type="button" 
        onclick="performDestroy({{$category->id}} , this)"
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
{{-- <div class="pagination-container">
    <ul class="pagination">
        <li>
            <a href="#">السابق</a>
        </li>

        <li class="active">
            <a href="#">1</a>
        </li>

        <li>
            <a href="#">2</a>
        </li>

        <li>
            <a href="#">3</a>
        </li>

        <li>
            <a href="#">التالي</a>
        </li>
    </ul>
</div> --}}

{{ $categories->links() }}



</div>


</div>



@endsection



@section('scripts')
<script>
    function performDestroy(id , reference){
        confirmDestroy('/cms/admin/categories/'+id , reference);
    }
</script>
@endsection