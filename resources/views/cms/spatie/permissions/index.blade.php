@extends('cms.parent')

@section('title', 'الصلاحيات الخاصة بالنظام')

@section('styles')
<style>
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

    .action-btn.btn-delete {
        background: #e17055;
    }

    .action-btn.btn-delete:hover {
        background: #d63031;
    }

    .badge-permission {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-permission {
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 500;
        margin: 2px;
        display: inline-block;
    }

    .count-badge {
        background: #f8f9fa;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #2d3436;
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
    }
</style>
@endsection

@section('main_content', 'الصلاحيات الخاصة بالنظام')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">🔐 إدارة الصلاحيات الخاصة بالنظام</h4>
            <p class="text-muted small">عرض وإدارة الصلاحيات الخاصة بالنظام في النظام</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('permissions.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> إضافة دور
            </a>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-key"></i> إضافة صلاحية
            </a>
        </div>
    </div>

    <!-- ===== الأدوار ===== -->
    <div class="table-container mb-4">
        <div class="table-header">
            <div>
                <h6 class="fw-bold mb-0">
                    <i class="fas fa-users-cog text-warning"></i> الأدوار
                </h6>
            </div>
            <div>
                <span class="count-badge">عدد الأدوار: <strong>{{ $permissions->count() }}</strong></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>اسم الدور</th>
                        <th>الـ Guard</th>
                        <th>عدد الصلاحيات</th>
                        <th>تاريخ الإضافة</th>
                        <th style="width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $permission->name }}</strong>
                            @if($permission->guard_name === 'admin')
                            <span class="badge bg-danger">أدمن</span>
                            @elseif($permission->guard_name === 'craftman')
                            <span class="badge bg-warning text-dark">حرفي</span>
                            @elseif($permission->guard_name === 'user')
                            <span class="badge bg-info text-white">مستخدم</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $permission->guard_name }}</span></td>
                        <td>
                            <span class="badge bg-primary">{{ $permission->permissions->count() }}</span>
                        </td>
                        <td>{{ $permission->created_at ? $permission->created_at->format('Y-m-d') : '-' }}</td>
                        <td>
                            <div class="action-btns">
                                @if($permission->guard_name != 'admin')
                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline" data-crud-delete data-confirm-message="لا يمكن التراجع عن حذف هذه الصلاحية.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="action-btn btn-delete" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                            لا توجد أدوار مضافة حالياً
                            <br>
                            <a href="{{ route('permissions.create') }}" class="btn btn-warning btn-sm mt-2">
                                <i class="fas fa-plus"></i> أضف أول دور
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===== الصلاحيات ===== -->

</div>
@endsection

@section('scripts')
<script>
    // ============================================
    // حذف (دور أو صلاحية)
    // ============================================

    console.log('✅ صفحة الصلاحيات الخاصة بالنظام جاهزة');
    console.log('📊 عدد الأدوار: {{ $permissions->count() }}');
</script>
@endsection