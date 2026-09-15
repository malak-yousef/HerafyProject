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

@section('main_content', 'عرض الصورة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-edit text-warning"></i> عرض الصورة
                    </h5>
                    <p class="text-muted small mb-0">عرض بيانات الصورة</p>
                </div>
                <a href="{{ route('order_images.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <div id="imageForm" onsubmit="event.preventDefault();">

                <div class="row g-4">

                    <div class="col-md-3">
                        <label class="form-label">الصورة الحالية</label>
                        <div class="image-preview">
                            @if($order_images->image_path)
                            <img src="{{ $order_images->publicUrl() }}" alt="{{ $order_images->order->title }}">
                            @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                                <span>عدل صورة</span>
                            </div>
                            @endif
                        </div>
                       
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="order_id">عنوان الطلب <span class="text-danger">*</span></label>
                                <select name="order_id" id="order_id" class="form-control" required>
                                    <option value="">اختر عنوان للطلب</option>
                                    @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ $order_images->order_id == $order->id ? 'selected' : '' }}>
                                        {{ $order->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            




 <div class="row mt-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-label"><i class="fas fa-calendar text-warning"></i> تاريخ الإضافة</span>
                        <span class="info-value">{{ $order_images->created_at ? $order_images->created_at->format('Y-m-d h:i A') : '-' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-label"><i class="fas fa-clock text-warning"></i> آخر تحديث</span>
                        <span class="info-value">{{ $order_images->updated_at ? $order_images->updated_at->format('Y-m-d h:i A') : '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4 pt-3 border-top">
                <a href="{{ route('order_images.edit', $order_images->id) }}" class="btn btn-warning btn-submit">
                    <i class="fas fa-edit"></i> تعديل
                </a>
                <button class="btn btn-danger btn-submit" onclick="performDestroy('{{ $order_images->id }}', this)">
                    <i class="fas fa-trash-alt"></i> حذف
                </button>
                <a href="{{ route('order_images.index') }}" class="btn btn-outline-secondary btn-back ms-auto">
                    <i class="fas fa-arrow-right"></i> العودة إلى القائمة
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/order_images/' + id, reference);
    }


    console.log('✅ صفحة عرض الصورة جاهزة');
</script>
@endsection
