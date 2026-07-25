@extends('cms.parent')

@section('title', 'تعديل منطقة')
@section('styles')
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
        transition: .25s;
    }

    .form-card .form-control:focus,
    .form-card .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
    }

    textarea.form-control {
        resize: none;
    }

    .form-card .form-control.is-invalid {
        border-color: #e17055;
    }

    .form-card .form-control.is-valid {
        border-color: #00b894;
    }

    .form-card .invalid-feedback {
        font-size: 13px;
        color: #e17055;
    }

    .form-card .btn-submit {
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .form-card .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .form-card .btn-back {
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .form-card .btn-back:hover {
        transform: translateY(-2px);


    }

    .btn-custom {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        transition: .25s;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .alert {
        border-radius: 10px;
        border: none;
        font-size: 13px;
    }

    .btn-back {
        min-width: 110px;
    }

    .form-control:hover,
    .form-control:hover {
        border-color: #f4b400;
    }
</style>



@endsection

@section('main_content' , 'تعديل المدينة ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                        <i class="fas fa-map-marked-alt text-warning"></i>
                        تعديل بيانات المدينة
                    </h4>


                    <!-- <p class="text-muted small mb-0">أدخل بيانات الحرفي الجديد لإضافته إلى المنصة</p> -->
                </div>
                <a href="{{ route('cities.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عنصر عرض الأخطاء (جديد) -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="editAreaForm" action="#"
                onsubmit="event.preventDefault(); performUpdate({{ $cities->id }});">

                <div class="row">


                    <!-- باقي الحقول -->
                    <div class="col-12">
                        <div class="row g-3">
                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> اسم المدينة <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="مثال: الرمال" value="{{ $cities->name }}" required>
                            </div>
                        </div>



                        <!-- أزرار الإجراء -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                            <button type="reset" class="btn btn-outline-secondary btn-custom">
                                <i class="fas fa-undo"></i> إعادة تعيين
                            </button>

                            <button type="submit" class="btn btn-warning btn-submit btn-custom">
                                <i class="fas fa-save"></i> حفظ التعديل
                            </button>

                            <a href="{{ route('cities.index') }}" class="btn btn-outline-danger btn-custom">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    function performUpdate(id) {
        let formData = new FormData();
        formData.append('name', document.getElementById('name').value);



        storeRoute('/cms/admin/cities_update/' + id, formData);
    }
</script>


@endsection