@extends('cms.parent')

@section('title', 'عرض الصورة')

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

.form-card .form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 10px 15px;
    background-color: #f8f9fa;
}

.image-display {
    width: 100%;
    max-width: 300px;
    height: 250px;
    border-radius: 15px;
    border: 2px solid #e9ecef;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.image-display img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-display .no-image {
    color: #adb5bd;
    text-align: center;
}

.image-display .no-image i {
    font-size: 3rem;
    display: block;
    margin-bottom: 10px;
}

.info-box {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px 15px;
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-right: 3px solid #FFC107;
}

.info-box .info-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
}

.info-box .info-value {
    font-size: 15px;
    font-weight: 600;
    color: #2d3436;
}

.btn-submit {
    padding: 12px 40px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

.btn-back {
    padding: 12px 30px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .form-card {
        padding: 20px;
    }
    .image-display {
        max-width: 100%;
        height: 200px;
    }
}
</style>
@endsection

@section('main_content', 'عرض الصورة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-image text-warning"></i> عرض الصورة
                    </h5>
                    <p class="text-muted small mb-0">{{ $portfolio_images->image_name }}</p>
                </div>
                <a href="{{ route('portfolio_images.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <label class="form-label">الصورة</label>
                    <div class="image-display">
                        @if($portfolio_images->image_path)
                            <img src="{{ asset('storage/images/portfolio_images/' . $portfolio_images->image_path) }}" alt="{{ $portfolio_images->image_name }}">
                        @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>لا توجد صورة</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tag text-warning"></i> اسم الصورة</label>
                            <input type="text" class="form-control" value="{{ $portfolio_images->image_name }}" disabled readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-folder text-warning"></i> المعرض</label>
                            <input type="text" class="form-control" value="{{ $portfolio_images->portfolio->title ?? 'غير محدد' }}" disabled readonly>
                        </div>

                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-align-left text-warning"></i> الوصف</label>
                            <textarea class="form-control" rows="3" disabled readonly>{{ $portfolio_images->image_description ?? 'لا يوجد وصف' }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-label"><i class="fas fa-calendar text-warning"></i> تاريخ الإضافة</span>
                        <span class="info-value">{{ $portfolio_images->created_at ? $portfolio_images->created_at->format('Y-m-d h:i A') : '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-label"><i class="fas fa-clock text-warning"></i> آخر تحديث</span>
                        <span class="info-value">{{ $portfolio_images->updated_at ? $portfolio_images->updated_at->format('Y-m-d h:i A') : '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4 pt-3 border-top">
                <a href="{{ route('portfolio_images.edit', $portfolio_images->id) }}" class="btn btn-warning btn-submit">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <form action="{{ route('portfolio_images.destroy', $portfolio_images->id) }}" method="POST" class="d-inline" data-crud-delete data-confirm-message="لا يمكن التراجع عن حذف هذه الصورة.">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-submit">
                        <i class="fas fa-trash-alt"></i> حذف
                    </button>
                </form>
                <a href="{{ route('portfolio_images.index') }}" class="btn btn-outline-secondary btn-back ms-auto">
                    <i class="fas fa-arrow-right"></i> العودة إلى القائمة
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>


    console.log('✅ صفحة عرض الصورة جاهزة');
</script>
@endsection