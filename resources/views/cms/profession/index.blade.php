@extends('cms.parent')

@section('title', 'المهن الرئيسية')


@section('styles')
<style>
    .professions-page {
        padding: 30px;
    }

    .profession-card {
        background: #fff;
        padding: 25px;
        border-radius: 18px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .08);
    }

    .table {
        vertical-align: middle;
    }

    .table thead th {
        background: #f8f9fa;
        color: #14213d;
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid #e9ecef;
    }

    .table tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
    }

    .status-active {
        background: #d1fae5;
        color: #15803d;
    }

    .status-inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    .add-btn {
        background: #ffbf00;
        color: #14213d;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: bold;
        transition: .3s;
    }

    .add-btn:hover {
        background: #e5ac00;
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
        transition: .3s;
    }

    .show-btn {
        background: #e8f1ff;
        color: #2563eb;
    }

    .edit-btn {
        background: #fff4dd;
        color: #f59e0b;
    }

    .delete-btn {
        background: #ffe5e5;
        color: #dc2626;
        border: none;
    }

    .action-btn:hover {
        transform: translateY(-3px);
        opacity: .9;
    }

    .actions-column {
        white-space: nowrap;
        width: 180px;
    }
</style>
@endsection



@section('main_content')

المهن

@endsection



@section('content')


<div class="container-fluid professions-page">


    <div class="card profession-card">


        <div class="d-flex justify-content-between mb-4">


            <h3>
                إدارة المهن
            </h3>




            <a href="{{ route('professions.create') }} ">
                <button class="add-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">

                    + إضافة مهنة

                </button>
            </a>


            <a href="{{ route('professions_trashed') }} ">
                <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">

                    سلة المحذوفات للمهن

                </button>
            </a>



        </div>



        <table class="table">


            <thead>

                <tr>

                    <th style="width: 15px">#</th>

                    <th class="text-center">اسم المهنة</th>
                    <th class="text-center">اسم التصنيف</th>
                    {{-- <th class="text-center">وصف التصنيف</th> --}}
                    <th class="text-center">الحالة</th>


                    <th class="text-center actions-column">عمليات</th>




                </tr>

            </thead>



            <tbody>


                @foreach($professions as $profession)


                <tr>

                    <td>
                        {{ $profession->id }}
                    </td>


                    <td>
                        {{ $profession->name }}
                    </td>

                    <td><span class="badge bg-info">
                            {{ $profession->category->name ?? "" }}
                    </td>


                    {{-- <td class="description-column">
    {{ $profession->description }}
                    </td> --}}
                    {{-- <td>
    {{ $profession->status == 'active' ? 'فعال' : 'غير فعال' }}
                    </td> --}}





                    <td>
                        @if($profession->status == 'active')
                        <span class="status-badge status-active">
                            فعال
                        </span>
                        @else
                        <span class="status-badge status-inactive">
                            غير فعال
                        </span>
                        @endif
                    </td>

                    <td class="text-center actions-column">

                        <!-- زر العرض Show -->
                        <a href="{{ route('professions.show', $profession->id) }}"
                            class="action-btn show-btn"
                            title="عرض">
                            <i class="fa-solid fa-eye"></i>
                        </a>


                        <!-- زر التعديل Edit -->
                        <a href="{{ route('professions.edit', $profession->id) }}"
                            class="action-btn edit-btn"
                            title="تعديل">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>


                        <!-- زر الحذف Delete -->
                        <form action="{{ route('professions.destroy', $profession->id) }}"
                            method="POST"
                            style="display:inline-block">

                            @csrf
                            @method('DELETE')

                            <button type="button"
                                onclick="performDestroy({{$profession->id}} , this)"
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

        {{ $professions->links() }}



    </div>


</div>



@endsection



@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/professions/' + id, reference);
    }
</script>
@endsection