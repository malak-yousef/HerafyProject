@extends('cms.parent')

@section('title', 'تعديل معرض ')


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

@section('main_content' , 'تعديل معرض ')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-images text-warning"></i> تعديل معرض
                    </h5>
                    <p class="text-muted small mb-0">عدل بيانات المعرض لإضافته إلى المنصة</p>
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
            <form id="editPortfolioForm" action="#" onsubmit="event.preventDefault(); performUpdate();">
                <div class="row g-4">
                    <!-- صورة المعرض -->
                    <div class="col-md-3">
                        <label class="form-label">صورة المعرض</label>
                        <div class="image-preview" onclick="document.getElementById('portfolioCover_image').click()">
                            @if($portfolios->portfolioCover_image)
                            <img id="imagePreview" src="{{ asset('storage/images/portfolio/' . $portfolios->portfolioCover_image) }}"
                                alt="صورة الحرفي" style="width:100%; height:100%; object-fit:cover;">
                            @else
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>عدل صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة المعرض">
                            @endif
                        </div>
                        <input type="file" class="form-control d-none" id="portfolioCover_image" name="portfolioCover_image" accept="image/*">
                        <small class="text-muted">صورة غلاف المعرض </small>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="craftman_id">اسم الحرفي
                                    <span class="text-danger">*</span></label>
                                <select name="craftman_id" id="craftman_id" class="form-control select2"
                                    style="width:100%;">
                                    @foreach ($craftmen as $craftman)
                                    <option @if ($craftman->id == $portfolios->craftman_id)selected @endif
                                        value="{{ $craftman->id }}">{{ $craftman->name }} </option>
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
                                    placeholder="مثال: خزانة ملابس خشب زان" value="{{$portfolios->title}}" required>
                            </div>

                            <!-- الموقع -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> الموقع
                                </label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="غزة - انصار" value="{{ $portfolios->location  }}">
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <label for="status" class="form_label"> حالة التفعيل </label>
                                    <select name="status" id="status" class="form-control select2"
                                        style="width:100%;">
                                        <option value="1" {{ $portfolios->status ==1 ? 'selected' : '' }}>فعال</option>
                                        <option value="0" {{ $portfolios->status ==0 ? 'selected' : '' }}>غير فعال
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن المشروع وتفاصيله...">{{$portfolios->description}}</textarea>
                            </div>

                        </div>
                    </div>
                </div>
        </div>

        <!-- أزرار الإجراء -->
        <div class="d-flex gap-3 mt-4 pt-3 border-top">
            <button type="button" onclick="performUpdate({{$portfolios->id}})" class="btn btn-warning btn-submit">
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
    function performUpdate(id) {
        let form = document.getElementById('editPortfolioForm');
        let formData = new FormData(form);
        storeRoute('/cms/admin/portfolios_update/' + id, formData);
    }

    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
                document.getElementById('imagePlaceholder').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection