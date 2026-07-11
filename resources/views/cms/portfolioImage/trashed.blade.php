@extends('cms.parent')

@section('title', 'سلة المحذوفات - صور المعارض')

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

    .action-btn.btn-back {
        background: #6c5ce7;
    }

    .action-btn.btn-back:hover {
        background: #5a4bd1;
    }

    @media (max-width: 768px) {
        .table-container {
            padding: 15px;
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

    .badge-trash {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-restore {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
</style>
@endsection

@section('main_content', 'سلة المحذوفات - صور المعارض')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">🗑️ سلة المحذوفات</h4>
            <p class="text-muted small">عرض الصور المحذوفة</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('portfolio_images.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة إلى القائمة
            </a>
            <button class="btn btn-danger btn-sm" onclick="destroyAll()">
                <i class="fas fa-trash-alt"></i> تفريغ السلة
            </button>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" id="searchImages"
                    placeholder="🔍 بحث عن صورة..." oninput="filterTable()">
            </div>
            <div>
                <span class="text-muted small">عدد الصور: </span>
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
                            <img src="{{ asset($portfolio_image->image_path) }}" alt="{{ $portfolio_image->image_name }}" class="portfolio-image">
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
                            <div class="action-btns">
                                <!-- عرض -->
                                <a href="{{ route('portfolio_images.show', $portfolio_image->id) }}" class="action-btn btn-view text-primary" 
                                    title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- استعادة -->
                                <a href="{{ route('portfolio_images_restore' , $portfolio_image->id) }}" class="action-btn btn-edit text-success"
                                    title="اعادة">
                                    <i class="fas fa-undo"></i>
                                </a>
                                <!-- حذف -->
                                <a href="{{ route('portfolio_images_force_destroy' , $portfolio_image->id) }}" class="action-btn btn-delete text-danger"
                                    title="حذف بشكل دائم">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
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


    </div>
</div>
@endsection

@section('scripts')
<script>
    function forceDestroy(id) {
        Swal.fire({
            title: '⚠️ حذف نهائي',
            text: 'سيتم حذف هذه الصورة نهائياً ولا يمكن استعادتها',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e17055',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، حذف نهائي',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/cms/admin/portfolio_images_force_destroy/' + id;
            }
        });
    }

    function destroyAll() {
        Swal.fire({
            title: '⚠️ تفريغ السلة',
            text: 'سيتم حذف جميع الصور نهائياً ولا يمكن استعادتها',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e17055',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، تفريغ الكل',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/cms/admin/portfolio_images_destroy_all';
            }
        });
    }


    console.log('✅ صفحة سلة المحذوفات جاهزة');
</script>
@endsection