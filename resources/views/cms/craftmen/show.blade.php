@extends('cms.parent')

@section('title', 'عرض حرفي')


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

    .image-preview {
        width: 150px;
        height: 150px;
        border: 3px dashed #D4AF37;
        border-radius: 50%;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        overflow: hidden;
        transition: .25s;
    }

    .image-preview:hover {
        border-color: #f1c40f;
        background: #fcfdfe;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }



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
    .form-select:hover {
        border-color: #f4b400;
    }
</style>
@endsection

@section('main_content' , 'عرض حرفي')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">
            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold mb-1" style="color:#2c3e50;">
                        <i class="fas fa-user-edit" style="color:#f1c40f;"></i>
                        عرض بيانات الحرفي
                    </h4>


                    <!-- <p class="text-muted small mb-0">أدخل بيانات الحرفي الجديد لإضافته إلى المنصة</p> -->
                </div>
                <a href="{{ route('craftmen.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- نموذج الإضافة -->
            <form id="createCraftsmanForm" action="#" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- الصورة الشخصية -->
                    <div class="col-12 mb-4">
                        <label class="form-label d-block text-center">الصورة الشخصية</label>
                        <div class="d-flex justify-content-center">
                            <div class="image-preview" onclick="document.getElementById('profile_image').click()">
                                @if($craftmen->user && $craftmen->user->image)
                                <img id="imagePreview" src="{{ $craftmen->user->profilePhotoUrl() }}"
                                    alt="صورة الحرفي" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                <div class="placeholder" id="imagePlaceholder">
                                    <i class="fas fa-camera"></i>
                                    <span>عدل صورة</span>
                                </div>
                                <img id="imagePreview" style="display: none;" alt="صورة الحرفي">
                                @endif
                            </div>
                            <input type="file" class="form-control d-none" id="profile_image" name="profile_image" accept="image/*" disabled readonly>
                        </div>
                    </div>
                    <!-- باقي الحقول -->
                    <div class="col-12">
                        <div class="row g-3">

                            <!-- الاسم الكامل -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user" style="color:#f1c40f;"></i> الاسم الكامل <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="مثال: أحمد " value="{{ $craftmen->user->name }}" required readonly disabled>
                            </div>

                            <!-- اسم المستخدم -->
                            <div class="col-md-6">
                                <label for="user_name" class="form-label">
                                    <i class="fas fa-user" style="color:#f1c40f;"></i> اسم المستخدم <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="user_name" name="user_name"
                                    placeholder="مثال: abdMash1" value="{{ $craftmen->user->user_name }}" required readonly disabled>
                            </div>

                            <!-- رقم الهوية -->
                            <div class="col-md-6">
                                <label for="id_number" class="form-label">
                                    <i class="fas fa-id-card" style="color:#f1c40f;"></i> رقم الهوية <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="id_number" name="id_number"
                                    placeholder="مثال: 123456789" value="{{ $craftmen->id_number }}" required readonly disabled>
                            </div>

                            <!-- الحرفة -->
                            <div class="col-md-6">
                                <label for="craft_name" class="form-label">
                                    <i class="fas fa-tools" style="color:#f1c40f;"></i> الحرفة <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="craft_name" name="craft_name"
                                    placeholder="مثال: نجارة" value="{{ $craftmen->profession->name}}" required readonly disabled>
                            </div>

                            <!-- البريد الإلكتروني -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope" style="color:#f1c40f;"></i> البريد الإلكتروني
                                </label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="example@email.com" value="{{ $craftmen->user->email }}" readonly disabled>
                            </div>

                            <!-- رقم الهاتف -->
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-mobile" style="color:#f1c40f;"></i> رقم الهاتف
                                </label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="0599 123 456" value="{{ $craftmen->user->mobile }}" readonly disabled>
                            </div>

                            <!-- كلمة المرور  -->
                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock" style="color:#f1c40f;"></i> كلمة المرور
                                </label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="لا يمكن عرض كلمة المرور" readonly disabled>
                            </div>

                            <!-- العنوان -->
                            <div class="col-md-6">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt" style="color:#f1c40f;"></i> العنوان
                                </label>
                                <input type="text" class="form-control" id="address" name="address"
                                    placeholder="مثال: شارع فلسطين، غزة" value="{{ $craftmen->user->address }}" readonly disabled>
                            </div>

                            <!-- سنوات الخبرة -->
                            <div class="col-md-4">
                                <label for="experience_years" class="form-label">
                                    <i class="fas fa-star" style="color:#f1c40f;"></i> سنوات الخبرة
                                </label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years"
                                    placeholder="8" min="0" value="{{ $craftmen->experience_years }}" readonly disabled>
                            </div>

                            <!-- السعر بالساعة -->
                            <div class="col-md-4">
                                <label for="hourly_rate" class="form-label">
                                    <i class="fas fa-clock" style="color:#f1c40f;"></i> السعر بالساعة (₪)
                                </label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" readonly disabled
                                    placeholder="50" min="0" step="0.5" value="{{ $craftmen->hourly_rate }}">
                            </div>

                            <!-- السعر باليوم -->
                            <div class="col-md-4">
                                <label for="daily_rate" class="form-label">
                                    <i class="fas fa-calendar-day" style="color:#f1c40f;"></i> السعر باليوم (₪)
                                </label>
                                <input type="number" class="form-control" id="daily_rate" name="daily_rate" readonly disabled
                                    placeholder="350" min="0" step="0.5" value="{{ $craftmen->daily_rate }}">
                            </div>

                            <!-- الجنس -->
                            <div class="col-6">
                                <label class="form-check-label" for="gender"> الجنس : </label>
                                <select class="form-control select2" id="gender" name="gender" style="width: 100%;" disabled readonly>
                                    <option value="male" {{ $craftmen->user->gender == 'male' ? 'selected' : '' }}>ذكر
                                    </option>
                                    <option value="female" {{ $craftmen->user->gender == 'female' ? 'selected' : ''}}>انثى
                                    </option>
                                </select>
                            </div>

                            <!-- حالة التفعيل -->
                            <div class="col-6">
                                <label class="form-check-label" for="status"> حالة الحساب : </label>
                                <select name="status" id="status" class="form-control select2" disabled readonly
                                    style="width:100%;">
                                    <option value="pending" {{ $craftmen->user->status == 'pending'  ? 'selected' : ''}}>قيد الانتظار</option>
                                    <option value="active" {{ $craftmen->user->status == 'active'   ? 'selected' : ''}}>فعال</option>
                                    <option value="inactive" {{ $craftmen->user->status == 'inactive' ? 'selected' : ''}}>غير فعال</option>
                                    <option value="blocked" {{ $craftmen->user->status == 'blocked'  ? 'selected' : ''}}>محظور</option>
                                </select>

                            </div>

                            <!-- الوصف -->
                            <div class="col-12">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left" style="color:#f1c40f;"></i> الوصف
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="اكتب وصفاً عن الحرفي وخبراته ومهاراته..." readonly disabled>{{ $craftmen->description }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراء -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('craftmen.edit', $craftmen->id) }}" class="btn btn-warning btn-submit">
                        <i class="fas fa-save"></i> تعديل
                    </a>
                    <!-- <button type="reset" class="btn btn-outline-secondary btn-back">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button> -->
                    <a href="{{ route('craftmen.index') }}" class="btn btn-outline-danger btn-back ms-auto">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                    <a href="{{ route('offers.index') }}"
   class="btn btn-info btn-sm">
    <i class="fa-solid fa-list"></i>
    العروض
</a>
                </div>
                <div class="card mt-4">

    <div class="card-header">
        <h5>
            <i class="fas fa-briefcase"></i>
            الخدمات التي ينفذها الحرفي
        </h5>
    </div>

    <div class="card-body">

        <table class="table table-bordered text-center">

            <thead>
                <tr>
                    <th>#</th>
                    <th>الطلب</th>
                    <th>الزبون</th>
                    <th>الحالة</th>
                    <th>الإجراء</th>
                </tr>
            </thead>

            <tbody>
     
            @forelse($craftmen->serviceJobs as $job)

                <tr>

                    <td>{{ $job->id }}</td>

                    <td>#{{ $job->order_id }}</td>

                    <td>
                        {{ $job->customer->user->name ?? '-' }}
                    </td>

                    <td>
                        <x-cms.status-badge :status="$job->status" />
                    </td>

                    <td>

                        <a href="{{ route('service-jobs.show', $job) }}"
                           class="btn btn-info btn-sm">

                            <i class="fas fa-eye"></i>
                            عرض

                        </a>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="5">
                        لا توجد خدمات لهذا الحرفي.
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
