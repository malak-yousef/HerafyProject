@extends('cms.parent')

@section('title', 'الحرفيون المحذوفون')

@section('styles')
<style>
/* تنسيق الصورة في الجدول */
.craftsman-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #FFC107;
}

.craftsman-avatar-sm {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

/* تنسيق البادجات */
.badge-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

/* تنسيق أزرار الإجراءات */
.action-btns {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: #fff;
    cursor: pointer;
    text-decoration: none;
}

.action-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
}

.action-btn.btn-edit {
    background: #6c5ce7;
}

.action-btn.btn-edit:hover {
    background: #5a4bd1;
}

.action-btn.btn-view {
    background: #fdcb6e;
    color: #2d3436;
}

.action-btn.btn-view:hover {
    background: #fdcb6e;
}

.action-btn.btn-toggle {
    background: #0984e3;
}

.action-btn.btn-toggle:hover {
    background: #0770c4;
}

.action-btn.btn-delete {
    background: #e17055;
}

.action-btn.btn-delete:hover {
    background: #d63031;
}

/* تنسيق التقييم بالنجوم */
.stars {
    color: #fdcb6e;
    letter-spacing: 2px;
    font-size: 14px;
}

.stars .empty {
    color: #dfe6e9;
}

/* تنسيق الجدول */
.table-container {
    background: #fff;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.table-container .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.table-container .table-header input {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 8px 15px;
    transition: all 0.3s ease;
    max-width: 300px;
}

.table-container .table-header input:focus {
    border-color: #FFC107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.table-container table th {
    background: #f8f9fa;
    color: #2d3436;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    white-space: nowrap;
    font-size: 13px;
}

.table-container table td {
    vertical-align: middle;
    font-size: 14px;
}

.table-responsive {
    max-height: 550px;
    overflow-y: auto;
}

.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #FFC107;
    border-radius: 10px;
}

/* تنسيق الـ Pagination */
.card-footer {
    background: transparent;
    border-top: 1px solid #e9ecef;
    padding: 15px 0 0;
}

.card-footer .pagination {
    margin-bottom: 0;
    justify-content: center;
}

.card-footer .pagination .page-link {
    border-radius: 8px;
    margin: 0 3px;
    color: #2d3436;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.card-footer .pagination .page-link:hover {
    background: #FFC107;
    border-color: #FFC107;
    color: #fff;
}

.card-footer .pagination .active .page-link {
    background: #FFC107;
    border-color: #FFC107;
    color: #fff;
}

/* تنسيق الجوال */
@media (max-width: 768px) {
    .table-container {
        padding: 15px;
    }

    .table-container .table-header {
        flex-direction: column;
        align-items: stretch;
    }

    .table-container .table-header input {
        max-width: 100%;
    }

    .action-btns {
        gap: 3px;
    }

    .action-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
}

/* تنسيق الصورة الافتراضية */
.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #FFC107;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 18px;
}
</style>
@endsection

@section('main_content', ' الحرفيون المحذوفون')

@section('content')
<div id="section-craftsmen" class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="d-flex gap-2">
            <a href="{{ route('craftmen.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> إضافة حرفي
            </a>
            <a class="btn btn-warning btn-sm" href="{{ route('craftmen_destroy_all') }}">
                <i class="fas fa-trash-alt"></i> حذف جميع الحرفيون
            </a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" id="searchCraftsmen"
                    placeholder="🔍 بحث عن حرفي..." oninput="filterTable('craftsmen')">
                <select class="form-select form-select-sm" id="filterStatus" onchange="filterTable('craftsmen')"
                    style="width: 150px;">
                    <option value="all">جميع الحالات</option>
                    <option value="active">فعال</option>
                    <option value="inactive">غير فعال</option>
                </select>
            </div>
            
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 60px;">الصورة</th>
                        <th>الاسم</th>
                        <th>الحرفة</th>
                        <th style="width: 80px;">الخبرة</th>
                        <th style="width: 120px;">التقييم</th>
                        <th style="width: 100px;">عدد التقييمات</th>
                        <th style="width: 120px;">تاريخ الانضمام</th>
                        <th style="width: 90px;">الحالة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="craftsmenTableBody">
                    @forelse($craftmen as $craftman)
                    <tr>
                        <td>{{ $craftman->id }}</td>
                        <td>
                            @if($craftman->profile_image)
                            <img src="{{ asset($craftman->profile_image) }}" alt="{{ $craftman->name }}"
                                class="craftsman-avatar">
                            @else
                            <span class="avatar-placeholder">
                                {{ substr($craftman->name, 0, 1) }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $craftman->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $craftman->email ?? 'لا يوجد بريد' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">{{ $craftman->craft_name }}</span>
                        </td>
                        <td>{{ $craftman->experience_years ?? 0 }} سنة</td>
                        <td>
                            <div class="stars">
                                @php
                                $fullStars = floor($craftman->rating ?? 0);
                                $halfStar = ($craftman->rating ?? 0) - $fullStars >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                                @endphp
                                @for($i = 0; $i < $fullStars; $i++) <i class="fas fa-star"></i>
                                    @endfor
                                    @if($halfStar)
                                    <i class="fas fa-star-half-alt"></i>
                                    @endif
                                    @for($i = 0; $i < $emptyStars; $i++) <i class="fas fa-star empty"></i>
                                        @endfor
                                        <span
                                            class="text-muted small">({{ number_format($craftman->rating ?? 0, 1) }})</span>
                            </div>
                        </td>
                        <td>{{ $craftman->total_reviews ?? 0 }}</td>
                        <td>{{ $craftman->created_at ? $craftman->created_at->format('Y-m-d') : '-' }}</td>
                        <td>
                            @if($craftman->is_active)
                            <span class="badge bg-success badge-status">✅ فعال</span>
                            @else
                            <span class="badge bg-danger badge-status">❌ غير فعال</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                {{-- عرض --}}
                                <a href="{{ route('craftmen.show' , $craftman->id) }}" class="action-btn btn-view"
                                    title="عرض">
                                    <button class="action-btn btn-view" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </a>

                                {{-- اعادة --}}
                                <a href="{{ route('craftmen_restore' , $craftman->id) }}" class="action-btn btn-edit"
                                    title="اعادة">
                                    <i class="fas fa-undo"></i>
                                </a>

                                {{-- حذف --}}
                                <a href="{{ route('craftmen_force_destroy' , $craftman->id) }}" class="action-btn btn-delete"
                                    title="حذف بشكل دائم">
                                    <i class="fas fa-trash-alt"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                            لا يوجد حرفيين مسجلين حالياً
                            <br>
                            <a href="{{ route('craftmen.create') }}" class="btn btn-warning btn-sm mt-2">
                                <i class="fas fa-plus"></i> أضف أول حرفي
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
       
    </div>
</div>
@endsection

@section('scripts')
<script>
// ============================================
// 1. عرض تفاصيل الحرفي
// ============================================
function viewCraftsman(id) {
    // جلب البيانات من الـ API
    fetch(`/admin/craftsmen/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.error,
                    confirmButtonColor: '#FFC107'
                });
                return;
            }

            // حساب التقييم بالنجوم
            const rating = data.rating || 0;
            const fullStars = Math.floor(rating);
            const hasHalf = rating - fullStars >= 0.5;
            let starsHTML = '';
            for (let i = 0; i < fullStars; i++) {
                starsHTML += '<i class="fas fa-star text-warning"></i>';
            }
            if (hasHalf) {
                starsHTML += '<i class="fas fa-star-half-alt text-warning"></i>';
            }
            for (let i = 0; i < 5 - fullStars - (hasHalf ? 1 : 0); i++) {
                starsHTML += '<i class="fas fa-star text-muted"></i>';
            }

            Swal.fire({
                title: `🔧 ${data.name}`,
                html: `
                        <div class="text-start">
                            <div class="text-center mb-3">
                                ${data.profile_image ? 
                                    `<img src="{{ asset('') }}${data.profile_image}" style="width:100px;height:100px;border-radius:50%;border:3px solid #FFC107;">` :
                                    `<div style="width:100px;height:100px;border-radius:50%;background:#FFC107;display:inline-flex;align-items:center;justify-content:center;font-size:40px;color:#fff;font-weight:700;">${data.name.charAt(0)}</div>`
                                }
                            </div>
                            <p><strong>الاسم:</strong> ${data.name}</p>
                            <p><strong>الحرفة:</strong> <span class="badge bg-warning text-dark">${data.craft_name}</span></p>
                            <p><strong>سنوات الخبرة:</strong> ${data.experience_years || 0} سنة</p>
                            <p><strong>التقييم:</strong> ${starsHTML} (${rating.toFixed(1)})</p>
                            <p><strong>عدد التقييمات:</strong> ${data.total_reviews || 0}</p>
                            <p><strong>البريد الإلكتروني:</strong> ${data.email || 'غير محدد'}</p>
                            <p><strong>رقم الهاتف:</strong> ${data.phone || 'غير محدد'}</p>
                            <p><strong>السعر بالساعة:</strong> ${data.hourly_rate ? data.hourly_rate + ' ₪' : 'غير محدد'}</p>
                            <p><strong>السعر باليوم:</strong> ${data.daily_rate ? data.daily_rate + ' ₪' : 'غير محدد'}</p>
                            ${data.description ? `<p><strong>الوصف:</strong> ${data.description}</p>` : ''}
                            <p><strong>الحالة:</strong> ${data.is_active ? '✅ فعال' : '❌ غير فعال'}</p>
                            <p><strong>تاريخ الانضمام:</strong> ${new Date(data.created_at).toLocaleDateString('ar')}</p>
                        </div>
                    `,
                confirmButtonColor: '#FFC107',
                confirmButtonText: 'إغلاق',
                width: '550px'
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء جلب البيانات',
                confirmButtonColor: '#FFC107'
            });
        });
}


function performDestroy(id, referance) {
    confirmDestroy('/cms/admin/craftmen/' + id, referance);
}
</script>
@endsection