@extends('cms.parent')

@section('title', 'المحذوفين')

@section('styles')
<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }




    /* تنسيق البادجات */

    /* بادج اسم الحرفة الداخلي */
    .badge-city {
        background-color: #f1f2f6 !important;
        color: #2f3542 !important;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    /* تنسيق أزرار الإجراءات */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        cursor: pointer;
        text-decoration: none;
        font-size: 13px;


    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }


    .action-btn.btn-edit {
        background: #eafaf1;
        color: #2ecc71;
    }

    .action-btn.btn-edit:hover {
        background: #2ecc71;
        color: #fff;
    }

    .action-btn.btn-delete {
        background: #fde8e8;
        color: #e74c3c;
    }

    .action-btn.btn-delete:hover {
        background: #e74c3c;
        color: #fff;
    }

    /*
    .action-btn.btn-toggle {
        background: #0984e3;
    }

    .action-btn.btn-toggle:hover {
        background: #0770c4;
    }*/



    /* تنسيق الجدول */
    .table-container {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        border: 1px solid #f1f2f6;
    }

    .table-container .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* شريط البحث والفلترة الاحترافي */
    .table-container .table-header .form-control,
    .table-container .table-header .form-select {
        border-radius: 10px;
        border: 1px solid #e4e7eb;
        padding: 9px 16px;
        font-size: 13px;
        color: #495057;
        background-color: #fff;
        transition: all 0.25s ease;
    }

    .table-container .table-header .form-control:focus,
    .table-container .table-header .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* هيدر الجدول */
    .table-container table th {
        background: #fafafa;
        color: #5a6578;
        font-weight: 600;
        border-bottom: 1px solid #f1f2f6;
        padding: 14px 12px;
        font-size: 13px;
        text-align: center;
    }

    .table-container table th:first-child,
    .table-container table th:nth-child(3) {
        text-align: right;
        /* محاذاة الرقم والاسم لليمين */
    }

    .table-container table td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #4e5664;
        font-size: 13.5px;
        border-bottom: 1px solid #f8f9fa;
        text-align: center;
    }

    .table-container table td:first-child,
    .table-container table td:nth-child(3) {
        text-align: right;
    }

    .table-hover tbody tr:hover {
        background-color: #fcfdfe;
    }

    .table-responsive {
        max-height: 550px;
        overflow-y: auto;
    }

    /* تحسين شريط التمرير (Scrollbar) */
    .table-responsive::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }

    /* أزرار الإضافة والتصدير العلوية */
    .btn-custom-header {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }

    .action-btn.btn-restore {
        background: #ebf3fe;
        color: #3b82f6;
    }

    .action-btn.btn-restore:hover {
        background: #3b82f6;
        color: #fff;
    }
</style>

@endsection

@section('main_content', 'المحذوفين')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">🔧 إدارة المناطق المحذوفة</h4>
            <p class="text-muted small mb-0">عرض وإدارة جميع المناطق المحذوفة</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('areas.create') }}" class="btn btn-success btn-custom-header text-white" style="background-color: #10ac84;">
                <i class="fas fa-plus"></i> إضافة منطقة
            </a>
            <a class="btn btn-warning btn-sm" href="{{ route('areas.index') }}">
                <i class="fas fa-arrow-left"></i> عودة
            </a>

            <a href="{{ route('areas_forceAll') }} ">
                <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">
                    حذف نهائي من سلة المحذوفات
                </button>
            </a>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>

                    <th>الاسم</th>

                    <th>المدينة</th>
                    <th>تاريخ الحذف</th>
                    <th style="width: 180px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="areasTableBody">
                @foreach($areas as $area)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        <strong>{{ $area->name ?? 'غير محدد' }}</strong>

                    </td>


                    <td>
                        <span class="badge bg-warning text-dark">{{ $area->city->name }}</span>
                    </td>

                    <td>
                        {{ $area->deleted_at?->format('Y-m-d') }}
                    </td>

                    <td>
                        <div class="action-btns">
                            {{-- عرض --}}
                            <a href="{{ route('areas.show', $area->id) }}" class="action-btn btn-view" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a
                                href="{{ route('areas_restore',$area->id) }}"
                                class="action-btn btn-restore">

                                <i class="fas fa-undo"></i>

                            </a>
                            <!-- زر الحذف Delete -->
                            <form action="{{ route('areas.destroy', $area->id) }}"
                                method="POST"
                                style="display:inline-block">

                                @csrf
                                @method('DELETE')


                                <a href="{{ route('areas_force', $area->id) }}"
                                    class="action-btn btn btn-danger"
                                    title="حذف نهائي"
                                    type="button">

                                    <i class="fa-solid fa-trash"></i>
                                </a>

                            </form>

                    </td>
                </tr>


                @endforeach
            </tbody>
        </table>
    </div>

</div>
</div>

@endsection

@section('scripts')
<scripts>
    function performDestroy(id , reference){
    confirmDestroy('/cms/admin/areas_force/'+id , reference);
    }

</scripts>
@endsection