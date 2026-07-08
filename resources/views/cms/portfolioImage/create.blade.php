@extends('cms.parent')

@section('title', 'إضافة صورة جديدة')

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

@section('main_content', 'إضافة صورة جديدة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-plus-circle text-warning"></i> إضافة صورة جديدة
                    </h5>
                    <p class="text-muted small mb-0">أضف صورة جديدة لمعرض الأعمال</p>
                </div>
                <a href="{{ route('portfolio_images.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <form id="imageForm" onsubmit="event.preventDefault(); performStore();">
                @csrf
                <div class="row g-4">

                    <div class="col-md-3">
                        <label class="form-label">صورة المعرض</label>
                        <div class="image-preview" onclick="document.getElementById('image_path').click()">
                            <div class="placeholder" id="imagePlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>اختر صورة</span>
                            </div>
                            <img id="imagePreview" style="display: none;" alt="صورة المعرض">
                        </div>
                        <input type="file" class="form-control d-none" id="image_path" name="image_path"
                            accept="image/*" required>
                        <small class="text-muted">اختر صورة للمعرض</small>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="portfolio_id">المعرض <span class="text-danger">*</span></label>
                                <select name="portfolio_id" id="portfolio_id" class="form-control" required>
                                    <option value="">اختر المعرض</option>
                                    @foreach($portfolios as $portfolio)
                                        <option value="{{ $portfolio->id }}">{{ $portfolio->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="image_name" class="form-label">
                                    <i class="fas fa-tag text-warning"></i> اسم الصورة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="image_name" name="image_name"
                                    placeholder="مثال: صورة المطبخ" value="{{ old('image_name') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="image_description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> وصف الصورة
                                </label>
                                <textarea class="form-control" id="image_description" name="image_description" rows="3"
                                    placeholder="وصف مختصر للصورة...">{{ old('image_description') }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-warning btn-submit">
                        <i class="fas fa-save"></i> حفظ وإضافة
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-back">
                        <i class="fas fa-undo"></i> إعادة تعيين
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
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
                document.getElementById('imagePlaceholder').style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }

    function performStore() {
        let formData = new FormData();
        formData.append('image_name', document.getElementById('image_name').value);
        formData.append('image_description', document.getElementById('image_description').value);
        formData.append('portfolio_id', document.getElementById('portfolio_id').value);
        formData.append('image_path', document.getElementById('image_path').files[0]);
        
        store('/cms/admin/portfolio_images', formData);
    }

    document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'إعادة تعيين',
            text: 'هل أنت متأكد من إعادة تعيين جميع الحقول؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FFC107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('imageForm').reset();
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('imagePlaceholder').style.display = 'block';
                Swal.fire({ icon: 'success', title: '✅ تم', text: 'تم إعادة تعيين النموذج', timer: 1000, showConfirmButton: false });
            }
        });
    });

    console.log('✅ صفحة إضافة صورة جاهزة');
</script>
@endsection