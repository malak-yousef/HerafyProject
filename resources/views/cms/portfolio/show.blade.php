@extends('cms.parent')

@section('title', 'عرض معرض الاعمال')

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
    background-color: #f8f9fa;
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

/* ===== تحسين عرض الصورة ===== */
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

/* ===== تحسين شكل الحقول المعطلة ===== */
.form-control[disabled],
.form-control[readonly] {
    background-color: #f8f9fa;
    cursor: default;
}

/* ===== تحسين شكل السلكت ===== */
select.form-control[disabled] {
    cursor: default;
}

/* ===== تنسيق البادجات ===== */
.badge-status {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* ===== تنسيق معلومات إضافية ===== */
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

/* ===== تحسين الجوال ===== */
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

@section('main_content', 'عرض معرض الاعمال')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">

            <!-- ===== عنوان الصفحة ===== -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-images text-warning"></i> عرض معرض الاعمال
                    </h5>
                    <p class="text-muted small mb-0">{{ $portfolios->title }}</p>
                </div>
                <a href="{{ route('portfolios.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- ===== عرض الأخطاء ===== -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- ===== النموذج ===== -->
            <form id="showPortfolioForm" action="#">
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
                        
                    </div>

                    <!-- ===== العمود الأيسر: التفاصيل ===== -->
                    <div class="col-md-9">
                        <div class="row g-3">

                            <!-- ===== اسم الحرفي ===== -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-user text-warning"></i> اسم الحرفي
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" disabled>
    <option selected>
        {{ $portfolios->craftman->user->name }}
    </option>
</select>
                            </div>

                            <!-- ===== العنوان ===== -->
                            <div class="col-md-6">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading text-warning"></i> عنوان المعرض
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="title" name="title" disabled readonly
                                    placeholder="مثال: خزانة ملابس خشب زان" value="{{ $portfolios->title }}" required>
                            </div>

                            <!-- ===== الموقع ===== -->
                            <div class="col-md-6">
                                <label for="location" class="form-label">
                                    <i class="fas fa-map-marker-alt text-warning"></i> الموقع
                                </label>
                                <input type="text" class="form-control" id="location" name="location" disabled readonly
                                    placeholder="غزة - انصار" value="{{ $portfolios->location }}">
                            </div>

                            <!-- ===== تاريخ المشروع ===== -->
                            <div class="col-md-6">
                                <label for="project_date" class="form-label">
                                    <i class="fas fa-calendar-check text-warning"></i> تاريخ المشروع
                                </label>
                                <input type="date" class="form-control" id="project_date" name="project_date" disabled readonly
                                    value="{{ $portfolios->project_date ? \Carbon\Carbon::parse($portfolios->project_date)->format('Y-m-d') : '-' }}">
                            </div>

                            <!-- ===== الوصف ===== -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left text-warning"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" disabled readonly
                                    placeholder="اكتب وصفاً عن المشروع وتفاصيله...">{{ $portfolios->description }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ===== معلومات إضافية ===== -->
                <div class="row mt-3">
                    <div class="col-md-12">
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
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-label">
                                <i class="fas fa-calendar text-warning"></i> تاريخ الإضافة
                            </span>
                            <span class="info-value">{{ $portfolios->created_at ? $portfolios->created_at->format('Y-m-d h:i A') : '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-label">
                                <i class="fas fa-clock text-warning"></i> آخر تحديث
                            </span>
                            <span class="info-value">{{ $portfolios->updated_at ? $portfolios->updated_at->format('Y-m-d h:i A') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- ===== أزرار الإجراء ===== -->
                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('portfolios.edit', $portfolios->id) }}" class="btn btn-warning btn-submit">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
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

@endsection