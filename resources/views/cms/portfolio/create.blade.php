@extends('cms.parent')

@section('title', 'اضافة معرض جديد')


@section('styles')
<style>
    .form-card {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .form-card .form-label {
        font-weight: 600;
        color: #2d3436;
    }

    .form-card .form-control,
    .form-card .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-card .form-control:focus,
    .form-card .form-select:focus {
        border-color: #FFC107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
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
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
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

    .image-preview {
        width: 150px;
        height: 150px;
        border-radius: 10px;
        border: 2px dashed #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        background: #f8f9fa;
    }

    .image-preview:hover {
        border-color: #FFC107;
        background: #fffdf5;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview .placeholder {
        color: #adb5bd;
        text-align: center;
    }

    .image-preview .placeholder i {
        font-size: 2rem;
        display: block;
        margin-bottom: 5px;
    }

    .image-preview .placeholder span {
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }

        .image-preview {
            width: 120px;
            height: 120px;
        }
    }
</style>
@endsection

@section('main_content' , 'اضافة معرض جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-images text-warning"></i> إضافة معرض جديد
                    </h5>
                    <p class="text-muted small mb-0">أدخل بيانات المعرض الجديد لإضافته إلى المنصة</p>
                </div>
                <a href="{{ route('portfolios.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عنصر عرض الأخطاء -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="portfolios-form" action="#" onsubmit="event.preventDefault(); performStore();">
                <div class="row g-4">
                    <!-- صورة المعرض -->
                    <div class="col-md-3">
                        <label class="form-label">صورة المعرض</label>
                        <div class="image-preview" onclick="document.getElementById('portfolioCover_image').click()">
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>اختر صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة المعرض">
                        </div>
                        <input type="file" class="form-control d-none" id="portfolioCover_image" name="portfolioCover_image" accept="image/*">
                        <small class="text-muted">صورة غلاف المعرض</small>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="craftman_id">اسم الحرفي
                                    <span class="text-danger">*</span></label>
                                <select name="craftman_id" id="craftman_id" class="form-control select2"
                                    style="width:100%;">
                                    @foreach ($craftmen as $craftman)
    <option value="{{ $craftman->id }}">
        {{ $craftman->user->name }}
    </option>
@endforeach

                                </select>
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading text-warning"></i> عنوان المعرض <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="مثال: خزانة ملابس خشب زان" value="{{ old('title') }}" required>
                            </div>
                             <input
    type="date"
    name="project_date"
    class="form-control">
                            <!-- الموقع -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> الموقع
                                </label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="غزة - انصار" value="{{ old('location') }}">
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-6">
                                <label class="form-check-label" for="status"> حالة المعرض : </label>
                                <select name="status" id="status" class="form-control select2"
                                    style="width:100%;">
                                    <option value="1">فعال</option>
                                    <option value="0">غير فعال</option>
                                </select>

                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن المشروع وتفاصيله...">{{ old('description') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>
        </div>

        <!-- أزرار الإجراء -->
        <div class="d-flex gap-3 mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-warning btn-submit">
                <i class="fas fa-save"></i> حفظ وإضافة
            </button>
            <button type="reset" class="btn btn-outline-secondary btn-back">
                <i class="fas fa-undo"></i> إعادة تعيين
            </button>
            <a href="{{ route('portfolios.index') }}" class="btn btn-outline-danger btn-back ms-auto">
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
    function performStore() {
        let formData = new FormData();
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('craftman_id', document.getElementById('craftman_id').value);
        formData.append('location', document.getElementById('location').value);
        formData.append('status', document.getElementById('status').value);
        formData.append('portfolioCover_image', document.getElementById('portfolioCover_image').files[0]);

        // استدعاء الدالة store لإرسال البيانات
        store('/cms/admin/portfolios', formData);


    }
</script>
@endsection