@extends('cms.parent')

@section('title', 'صور معرض الاعمال')

@section('styles')
<style>
.portfolio-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.image-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    background: #f8f9fa;
    border: 2px dashed #e9ecef;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 24px;
}

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

.action-btn.btn-restore {
    background: #00b894;
}
.action-btn.btn-restore:hover {
    background: #00a381;
}

.action-btn.btn-force {
    background: #e17055;
}
.action-btn.btn-force:hover {
    background: #d63031;
}

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
    .action-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    .portfolio-image {
        width: 50px;
        height: 50px;
    }
}
</style>
@endsection

@section('main_content', 'صور معرض الاعمال')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">🖼️ إدارة صور المعارض</h4>
            <p class="text-muted small">عرض وإدارة جميع صور معارض الحرفيين</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('portfolio_images.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> إضافة صورة
            </a>
            <a href="{{ route('portfolio_images_trashed') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-trash-alt"></i> سلة المحذوفات
            </a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" id="searchImages"
                    placeholder="🔍 بحث عن صورة..." oninput="filterTable()">
            </div>
            <div>
                <span class="text-muted small">عدد الصور: <strong>{{ $portfolio_images->total() }}</strong></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 100px;">الصورة</th>
                        <th>اسم الصورة</th>
                        <th>الوصف</th>
                        <th>المعرض</th>
                        <th style="width: 150px;">تاريخ الإضافة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="imagesTableBody">
                    @forelse($portfolio_images as $portfolio_image)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($portfolio_image->image_path)
                                <img src="{{ asset('storage/images/portfolio_images/' . $portfolio_image->image_path) }}" alt="{{ $portfolio_image->image_name }}" class="portfolio-image" width="80" height="80" loading="lazy" decoding="async">
                            @else
                                <span class="image-placeholder">
                                    <i class="fas fa-image"></i>
                                </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $portfolio_image->image_name }}</strong>
                        </td>
                        <td>{{ Str::limit($portfolio_image->image_description, 30) ?? '-' }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ $portfolio_image->portfolio->title ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td>{{ $portfolio_image->created_at ? $portfolio_image->created_at->format('Y-m-d') : '-' }}</td>
                        <td>
                            <x-cms.table-actions :show-url="route('portfolio_images.show', $portfolio_image)" :edit-url="route('portfolio_images.edit', $portfolio_image)" :delete-url="route('portfolio_images.destroy', $portfolio_image)" show-permission="portfolio-images.view" edit-permission="portfolio-images.update" delete-permission="portfolio-images.delete" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-images fa-2x mb-2 d-block"></i>
                            لا توجد صور مضافة حالياً
                            <br>
                            <a href="{{ route('portfolio_images.create') }}" class="btn btn-warning btn-sm mt-2">
                                <i class="fas fa-plus"></i> أضف أول صورة
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($portfolio_images->hasPages())
        <div class="card-footer clearfix">
            {{ $portfolio_images->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/portfolio_images/' + id, reference);
    }

    function filterTable() {
        const searchText = document.getElementById('searchImages').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#imagesTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(3) strong')?.textContent?.toLowerCase() || '';
            const description = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
            const portfolio = row.querySelector('td:nth-child(5) .badge')?.textContent?.toLowerCase() || '';

            let show = true;
            if (searchText && !name.includes(searchText) && !description.includes(searchText) && !portfolio.includes(searchText)) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        const noResults = document.getElementById('noResultsMessage');
        if (visibleCount === 0) {
            if (!noResults) {
                const tbody = document.getElementById('imagesTableBody');
                const tr = document.createElement('tr');
                tr.id = 'noResultsMessage';
                tr.innerHTML = `
                    <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-search fa-2x mb-2 d-block"></i>
                        لا توجد نتائج تطابق البحث
                    </td>
                `;
                tbody.appendChild(tr);
            }
        } else {
            if (noResults) noResults.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchImages')?.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') filterTable();
        });
        console.log('✅ صفحة صور المعارض جاهزة');
    });

</script>
@endsection
