@extends('cms.parent')

@section('title', 'تعديل الصورة')

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
        border: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #f8f9fa;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview .no-image {
        color: #adb5bd;
        text-align: center;
    }

    .image-preview .no-image i {
        font-size: 2rem;
        display: block;
        margin-bottom: 5px;
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

@section('main_content', 'تعديل الصورة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-edit text-warning"></i> تعديل الصورة
                    </h5>
                    <p class="text-muted small mb-0">تعديل بيانات الصورة</p>
                </div>
                <a href="{{ route('portfolio_images.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <form id="imageForm" onsubmit="event.preventDefault(); performUpdate()">

                <div class="row g-4">

                    <div class="col-md-3">
                        <label class="form-label">الصورة الحالية</label>
                        <div class="image-preview">
                            @if($portfolio_images->image_path)
                            <img src="{{ asset('storage/images/portfolio_images/' . $portfolio_images->image_path) }}" alt="{{ $portfolio_images->image_name }}">
                            @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>عدل صورة</span>
                            </div>
                            @endif
                        </div>
                        <div class="mt-2">
                            <label class="form-label">تغيير الصورة (اختياري)</label>
                            <input type="file" class="form-control" id="image_path" name="image_path" accept="image/*">
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="portfolio_id">المعرض <span class="text-danger">*</span></label>
                                <select name="portfolio_id" id="portfolio_id" class="form-control" required>
                                    <option value="">اختر المعرض</option>
                                    @foreach($portfolios as $portfolio)
                                    <option value="{{ $portfolio->id }}" {{ $portfolio_images->portfolio_id == $portfolio->id ? 'selected' : '' }}>
                                        {{ $portfolio->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="image_name" class="form-label">
                                    <i class="fas fa-tag text-warning"></i> اسم الصورة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="image_name" name="image_name"
                                    value="{{ $portfolio_images->image_name }}" required>
                            </div>

                            <div class="col-12">
                                <label for="image_description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> وصف الصورة
                                </label>
                                <textarea class="form-control" id="image_description" name="image_description" rows="3">{{ $portfolio_images->image_description }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">

                    <button type="button" class="btn btn-warning btn-submit" onclick="performUpdate({{$portfolio_images->id}})">
                        <i class="fas fa-save"></i> تحديث
                    </button>

                    <a href="{{ route('portfolio_images.index') }}" class="btn btn-outline-danger btn-back ms-auto">
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
    function performUpdate() {
        let formData = new FormData();
        formData.append('portfolio_id', document.getElementById('portfolio_id').value);
        formData.append('image_name', document.getElementById('image_name').value);
        formData.append('image_description', document.getElementById('image_description').value);
        formData.append('image_path', document.getElementById('image_path').files[0]);

        update('/cms/admin/portfolio_images/{{ $portfolio_images->id }}', formData);

    }

    console.log('✅ صفحة تعديل الصورة جاهزة');
</script>
@endsection