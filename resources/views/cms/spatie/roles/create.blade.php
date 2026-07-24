@extends('cms.parent')

@section('title', 'إضافة دور جديد')

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

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }
    }
</style>
@endsection

@section('main_content', 'إضافة دور جديد')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="form-card">

            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-plus text-warning"></i> إضافة دور جديد
                    </h5>
                    <p class="text-muted small mb-0">أدخل بيانات الدور الجديد</p>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-back">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>

            <!-- عرض الأخطاء -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <!-- نموذج الإضافة -->
            <form id="roleForm" onsubmit="event.preventDefault(); performStore();">
                @csrf

                <div class="row g-3">

                    <!-- اسم الدور -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag text-warning"></i> اسم الدور <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="مثال: admin" value="{{ old('name') }}" required>
                        <small class="text-muted">اسم الدور (يستخدم للتحقق في الكود)</small>
                    </div>

                    <!-- الـ Guard -->
                    <div class="col-md-6">
                        <label for="guard_name" class="form-label">
                            <i class="fas fa-shield-alt text-warning"></i> الـ Guard <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="guard_name" name="guard_name" required>
                            <option value="">اختر الـ Guard</option>
                            <option value="admin" {{ old('guard_name')    == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ old('guard_name')     == 'user' ? 'selected' : '' }}>User</option>
                            <option value="craftman" {{ old('guard_name') == 'craftman' ? 'selected' : '' }}>Craftman</option>
                        </select>
                        <small class="text-muted">نوع الحماية المستخدمة</small>
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
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-danger btn-back ms-auto">
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
    // ============================================
    // حفظ البيانات
    // ============================================
    function performStore() {
        let formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('guard_name', document.getElementById('guard_name').value);

        store('/cms/admin/roles', formData);
    }

    // ============================================
    // إعادة تعيين النموذج
    // ============================================
    document.querySelector('button[type="reset"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'إعادة تعيين',
            text: 'هل أنت متأكد من إعادة تعيين جميع الحقول؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#FFC107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، إعادة تعيين',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('roleForm').reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                Swal.fire({
                    icon: 'success',
                    title: '✅ تم',
                    text: 'تم إعادة تعيين النموذج',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        });
    });


    console.log('✅ صفحة إضافة دور جديد جاهزة');
</script>
@endsection