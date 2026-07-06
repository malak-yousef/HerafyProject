@extends('cms.parent')

@section('title', 'معرض أعمال الحرفيين')

@section('styles')
<style>
/* تنسيق الصورة في الجدول */
.portfolio-image {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.portfolio-image-sm {
    width: 40px;
    height: 40px;
    border-radius: 8px;
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

.action-btn.btn-delete {
    background: #e17055;
}

.action-btn.btn-delete:hover {
    background: #d63031;
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
.image-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    background: #f8f9fa;
    border: 2px dashed #e9ecef;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 20px;
}
</style>
@endsection

@section('main_content', 'معرض أعمال الحرفيين')

@section('content')
<div id="section-portfolios" class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">🖼️ إدارة معارض الحرفيين</h4>
            <p class="text-muted small">عرض وإدارة جميع معارض الحرفيين المسجلين</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('portfolios.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> إضافة معرض
            </a>
            <button class="btn btn-warning btn-sm" onclick="exportData('portfolios')">
                <i class="fas fa-download"></i> تصدير
            </button>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" id="searchPortfolios"
                    placeholder="🔍 بحث عن معرض..." oninput="filterTable('portfolios')">
                <select class="form-select form-select-sm" id="filterStatus" onchange="filterTable('portfolios')"
                    style="width: 150px;">
                    <option value="all">جميع الحالات</option>
                    <option value="active">فعال</option>
                    <option value="inactive">غير فعال</option>
                </select>
            </div>
            <div>
                <span class="text-muted small">عدد المعارض: <strong>{{ $portfolios->total() }}</strong></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 80px;">الصورة</th>
                        <th>عنوان المعرض</th>
                        <th>الحرفي</th>
                        <th>الحرفة</th>
                        <th style="width: 100px;">تاريخ المشروع</th>
                        <th style="width: 90px;">المشاهدات</th>
                        <th style="width: 90px;">الحالة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="portfoliosTableBody">
                    @forelse($portfolios as $portfolio)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($portfolio->portfolioCover_image)
                            <img src="{{ asset('storage/images/portfolio/' . $portfolio->portfolioCover_image) }}" alt="{{ $portfolio->title }}"
                                class="portfolio-image">
                            @else
                            <span class="image-placeholder">
                                <i class="fas fa-image"></i>
                            </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $portfolio->title }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($portfolio->description ?? '', 30) }}</small>
                        </td>
                        <td>
                            <strong>{{ $portfolio->craftman->name ?? 'غير محدد' }}</strong>
                        </td>
                        <td>
                            <span
                                class="badge bg-warning text-dark">{{ $portfolio->craftman->craft_name ?? 'غير محدد' }}</span>
                        </td>
                        <td>{{ $portfolio->project_date ? \Carbon\Carbon::parse($portfolio->project_date)->format('Y-m-d') : '-' }}
                        </td>
                        <td>
                            <i class="fas fa-eye text-muted"></i>
                            {{ $portfolio->views_count ?? 0 }}
                        </td>
                        <td>
                            @if($portfolio->is_active)
                            <span class="badge bg-success badge-status">✅ فعال</span>
                            @else
                            <span class="badge bg-danger badge-status">❌ غير فعال</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btns">
                                {{-- عرض --}}
                                <a href="{{ route('portfolios.show', $portfolio->id) }}" class="action-btn btn-view"
                                    title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- تعديل --}}
                                <a href="{{ route('portfolios.edit', $portfolio->id) }}" class="action-btn btn-edit"
                                    title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>



                                {{-- حذف --}}
                                <button class="action-btn btn-delete"
                                    onclick="performDestroy('{{ $portfolio->id }}', this)" title="حذف">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="fas fa-images fa-2x mb-2 d-block"></i>
                            لا يوجد معارض مضافة حالياً
                            <br>
                            <a href="{{ route('portfolios.create') }}" class="btn btn-warning btn-sm mt-2">
                                <i class="fas fa-plus"></i> أضف أول معرض
                            </a>
                            <a href="{{ route('portfolios_trashed') }}" class="btn btn-danger btn-sm mt-2"><i class="fas fa-trash-alt"></i> المعارض المحذوفة"></a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($portfolios->hasPages())
        <div class="card-footer clearfix">
            {{ $portfolios->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// ============================================
// 1. تبديل حالة المعرض
// ============================================
function togglePortfolioStatus(id, currentStatus) {
    const newStatus = !currentStatus;
    const statusText = newStatus ? 'تفعيل' : 'تعطيل';

    Swal.fire({
        title: `⚠️ ${statusText} المعرض`,
        text: `هل أنت متأكد من ${statusText} هذا المعرض؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#FFC107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `نعم، ${statusText}`,
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cms/admin/portfolios/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '✅ تم التحديث',
                            text: `تم ${data.is_active ? 'تفعيل' : 'تعطيل'} المعرض بنجاح`,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: data.message || 'حدث خطأ',
                            confirmButtonColor: '#FFC107'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء تنفيذ العملية',
                        confirmButtonColor: '#FFC107'
                    });
                });
        }
    });
}

// ============================================
// 2. حذف المعرض
// ============================================
function performDestroy(id, reference) {
    confirmDestroy('/cms/admin/portfolios/' + id, reference);
}

// ============================================
// 3. فلترة الجدول
// ============================================
function filterTable() {
    const searchText = document.getElementById('searchPortfolios').value.toLowerCase().trim();
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#portfoliosTableBody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const title = row.querySelector('td:nth-child(3) strong')?.textContent?.toLowerCase() || '';
        const craftman = row.querySelector('td:nth-child(4) strong')?.textContent?.toLowerCase() || '';
        const craft = row.querySelector('td:nth-child(5) .badge')?.textContent?.toLowerCase() || '';
        const statusBadge = row.querySelector('td:nth-child(8) .badge')?.textContent?.trim() || '';
        const isActive = statusBadge.includes('فعال');

        let show = true;

        if (searchText && !title.includes(searchText) && !craftman.includes(searchText) && !craft.includes(
                searchText)) {
            show = false;
        }

        if (statusFilter !== 'all') {
            if (statusFilter === 'active' && !isActive) show = false;
            if (statusFilter === 'inactive' && isActive) show = false;
        }

        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // عرض رسالة إذا لم توجد نتائج
    const noResults = document.getElementById('noResultsMessage');
    if (visibleCount === 0) {
        if (!noResults) {
            const tbody = document.getElementById('portfoliosTableBody');
            const tr = document.createElement('tr');
            tr.id = 'noResultsMessage';
            tr.innerHTML = `
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2 d-block"></i>
                        لا توجد نتائج تطابق البحث
                    </td>
                `;
            tbody.appendChild(tr);
        }
    } else {
        if (noResults) {
            noResults.remove();
        }
    }
}

// ============================================
// 4. تصدير البيانات
// ============================================
function exportData(type) {
    Swal.fire({
        icon: 'info',
        title: 'جاري التصدير',
        text: 'سيتم تصدير جميع البيانات',
        timer: 1000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = `/cms/admin/${type}/export`;
    });
}

// ============================================
// 5. إضافة مستمع للأحداث
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('searchPortfolios')?.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterTable();
        }
    });

    console.log('✅ صفحة معارض الحرفيين جاهزة');
    console.log('📊 عدد المعارض: {{ $portfolios->total() }}');
});

// ============================================
// 6. عرض رسائل الـ Session
// ============================================
// @if(session('success'))
// Swal.fire({
//     icon: 'success',
//     title: '✅ {{ session('
//     success ') }}',
//     timer: 2000,
//     showConfirmButton: false
// });
// @endif

// @if(session('error'))
// Swal.fire({
//     icon: 'error',
//     title: '⚠️ {{ session('
//     error ') }}',
//     confirmButtonColor: '#FFC107'
// });
// @endif
</script>
@endsection