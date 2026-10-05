@extends('cms.parent')

@section('title', 'عرض زبون')

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
        background-color: #f8f9fa;
    }

    .image-display {
        width: 100%;
        max-width: 250px;
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

    .badge-status {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
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

@section('main_content', 'عرض زبون')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">

            <!-- عنوان الصفحة -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-user-circle text-warning"></i> عرض زبون
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('customers.edit', $customers->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right"></i> العودة
                    </a>
                </div>
            </div>

            <!-- عرض الأخطاء -->
            <div id="error_alert" class="alert alert-danger" hidden>
                <ul id="error_messages_ul"></ul>
            </div>

            <div class="row g-4">

                <!-- الصورة -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">الصورة الشخصية</label>
                    <div class="image-display">
                        @if($customers->user && $customers->user->image)
                        <img src="{{ $customers->user->profilePhotoUrl() }}" alt="{{ $customers->user->name }}">
                        @else
                        <div class="no-image">
                            <i class="fas fa-user"></i>
                            <span>لا توجد صورة</span>
                        </div>
                        @endif
                    </div>

                    <!-- معلومات إضافية -->
                    <div class="mt-4">
                        <div class="info-box">
                            <span class="info-label"><i class="fas fa-calendar text-warning"></i> تاريخ الإضافة</span>
                            <span class="info-value">{{ $customers->created_at ? $customers->created_at->format('Y-m-d h:i A') : '-' }}</span>
                        </div>
                        <div class="info-box">
                            <span class="info-label"><i class="fas fa-clock text-warning"></i> آخر تحديث</span>
                            <span class="info-value">{{ $customers->updated_at ? $customers->updated_at->format('Y-m-d h:i A') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- التفاصيل -->
                <div class="col-md-8">
                    <div class="row g-3">

                        <!-- الاسم -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-user text-warning"></i> الاسم الكامل</label>
                            <input type="text" class="form-control" value="{{ $customers->user->name ?? '-' }}" disabled readonly>
                        </div>

                        <!-- اسم المستخدم -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-user-tag text-warning"></i> اسم المستخدم</label>
                            <input type="text" class="form-control" value="{{ $customers->user->user_name ?? '-' }}" disabled readonly>
                        </div>

                        <!-- البريد الإلكتروني -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-envelope text-warning"></i> البريد الإلكتروني</label>
                            <input type="text" class="form-control" value="{{ $customers->user->email ?? '-' }}" disabled readonly>
                        </div>

                        <!-- رقم الهوية -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-id-card text-warning"></i> رقم الهوية</label>
                            <input type="text" class="form-control" value="{{ $customers->id_number ?? '-' }}" disabled readonly>
                        </div>

                        <!-- رقم الهاتف -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-phone text-warning"></i> رقم الهاتف</label>
                            <input type="text" class="form-control" value="{{ $customers->user->mobile ?? '-' }}" disabled readonly>
                        </div>

                        <!-- العنوان -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-map-marker-alt text-warning"></i> العنوان</label>
                            <input type="text" class="form-control" value="{{ $customers->user->address ?? '-' }}" disabled readonly>
                        </div>

                        <!-- تاريخ الميلاد -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-calendar text-warning"></i> تاريخ الميلاد</label>
                            <input type="text" class="form-control" value="{{ $customers->user->date ? $customers->user->date : '-' }}" disabled readonly>
                        </div>

                        <!-- الجنس -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-venus-mars text-warning"></i> الجنس</label>
                            <input type="text" class="form-control" value="{{ $customers->user->gender == 'male' ? 'ذكر' : ($customers->user->gender == 'female' ? 'أنثى' : '-') }}" disabled readonly>
                        </div>

                        <!-- الحالة -->
                        <div class="col-md-12">
                            <label class="form-label"><i class="fas fa-toggle-on text-warning"></i> حالة الحساب</label>
                            <div>
                                @if($customers->user && $customers->user->status == 'active')
                                <span class="badge bg-success badge-status">✅ فعال</span>
                                @elseif($customers->user && $customers->user->status == 'inactive')
                                <span class="badge bg-danger badge-status">❌ غير فعال</span>
                                @elseif($customers->user && $customers->user->status == 'pending')
                                <span class="badge bg-warning text-dark badge-status">⏳ قيد التنفيذ</span>
                                @elseif($customers->user && $customers->user->status == 'blocked')
                                <span class="badge bg-dark badge-status">🚫 محظور</span>
                                @else
                                <span class="badge bg-secondary badge-status">غير محدد</span>
                                @endif
                            </div>
                        </div>


                    </div>
                </div>
                <hr class="my-4">


                <h5 class="fw-bold mb-3">
                    <i class="fas fa-list text-warning"></i>
                    طلبات الزبون
                </h5>


                <div class="table-responsive">

                    <table class="table table-hover align-middle text-center">
                        <thead>

                            <tr>

                                <th>العنوان</th>
                                <th>المدينة</th>
                                <th>الميزانية</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>

                            </tr>

                        </thead>


                        <tbody>


                            @forelse($customers->orders as $order)

                            <tr>

                                <td>
                                    {{ $order->title }}
                                </td>


                                <td>
                                    {{ $order->city->name ?? '-' }}
                                </td>

                                <td>
                                    <x-money :value="$order->budget" />
                                </td>

                                <td>

                                    <x-cms.status-badge :status="$order->status" />

                                </td>


                                </td>


                                <td>
                                    {{ $order->created_at->format('Y-m-d') }}
                                </td>


                            </tr>


                            @empty

                            <tr>

                                <td colspan="5" class="text-center text-muted">

                                    لا يوجد طلبات لهذا الزبون

                                </td>

                            </tr>


                            @endforelse


                        </tbody>


                    </table>

                </div>
<div class="card mt-4">

    <div class="card-header">
        <h5>
            <i class="fas fa-briefcase"></i>
            خدمات الزبون
        </h5>
    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered text-center">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطلب</th>
                        <th>الحرفي</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($customers->serviceJobs as $job)

                    <tr>

                        <td>{{ $job->id }}</td>

                        <td>
                            #{{ $job->order_id }}
                        </td>

                        <td>
                            {{ $job->craftman->user->name ?? '-' }}
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
                            لا توجد خدمات لهذا الزبون.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

                <!-- أزرار الإجراء -->
                <div class="d-flex gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('customers.edit', $customers->id) }}" class="btn btn-warning btn-submit">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <form action="{{ route('customers.destroy', $customers->id) }}" method="POST" class="d-inline" data-crud-delete data-confirm-message="لا يمكن التراجع عن حذف هذا العميل.">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-submit">
                            <i class="fas fa-trash-alt"></i> حذف
                        </button>
                    </form>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-back ms-auto">
                        <i class="fas fa-arrow-right"></i> العودة إلى القائمة
                    </a>
                </div>

            </div>
        </div>
    </div>
    @endsection

    @section('scripts')
    <script>
        // ============================================
        // حذف الزبون
        // ============================================

        console.log('✅ صفحة عرض الزبون جاهزة');
    </script>
    @endsection
