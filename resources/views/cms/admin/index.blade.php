@extends('cms.parent')

@section('title', 'المشرفين الرئيسية')

@section('styles')
<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }

    /* تنسيق الصورة في الجدول */
    .admins-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #FFC107;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .avatar-placeholder {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f1f2f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #57606f;
        font-weight: 700;
        font-size: 16px;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* تنسيق البادجات */
    .badge-status {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
    }

    .badge-status.bg-success {
        background-color: #e8f8f2 !important;
        color: #1e7e34 !important;
    }

    .badge-status.bg-warning {
        background-color: #fef5e7 !important;
        color: #b7791f !important;
    }

    .badge-status.bg-danger {
        background-color: #fde8e8 !important;
        color: #c53030 !important;
    }

    .badge-status.bg-dark {
        background-color: #edf2f7 !important;
        color: #4a5568 !important;
    }

    /* أزرار الإجراءات */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.25s ease;
        cursor: pointer;
        text-decoration: none;
        font-size: 13px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .action-btn.btn-view {
        background: #ebf3fe;
        color: #3b82f6;
    }

    .action-btn.btn-view:hover {
        background: #3b82f6;
        color: #fff;
    }

    .action-btn.btn-edit {
        background: #eafaf1;
        color: #2ecc71;
    }

    .action-btn.btn-edit:hover {
        background: #2ecc71;
        color: #fff;
    }

    .action-btn.btn-delete {
        background: #fde8e8;
        color: #e74c3c;
    }

    .action-btn.btn-delete:hover {
        background: #e74c3c;
        color: #fff;
    }

    /* تنسيق الجدول */
    .table-container {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        border: 1px solid #f1f2f6;
    }

    .table-container .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-container .table-header .form-control,
    .table-container .table-header .form-select {
        border-radius: 10px;
        border: 1px solid #e4e7eb;
        padding: 9px 16px;
        font-size: 13px;
        color: #495057;
        background-color: #fff;
        transition: all 0.25s ease;
    }

    .table-container .table-header .form-control:focus,
    .table-container .table-header .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    /* هيدر الجدول */
    .table-container table th {
        background: #fafafa;
        color: #5a6578;
        font-weight: 600;
        border-bottom: 1px solid #f1f2f6;
        padding: 14px 12px;
        font-size: 13px;
        text-align: center;
    }

    .table-container table th:first-child,
    .table-container table th:nth-child(3) {
        text-align: right;
    }

    .table-container table td {
        vertical-align: middle;
        padding: 14px 12px;
        color: #4e5664;
        font-size: 13.5px;
        border-bottom: 1px solid #f8f9fa;
        text-align: center;
    }

    .table-container table td:first-child,
    .table-container table td:nth-child(3) {
        text-align: right;
    }

    .table-hover tbody tr:hover {
        background-color: #fcfdfe;
    }

    .table-responsive {
        max-height: 550px;
        overflow-y: auto;
    }

    .table-responsive::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }

    .btn-custom-header {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }
</style>
@endsection

@section('main_content' , 'المشرفين')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color:#2c3e50;">إدارة المشرفين</h4>
            <p class="text-muted small mb-0">عرض وإدارة جميع المشرفين المسجلين في النظام</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admins.create') }}" class="btn btn-success btn-custom-header text-white" style="background:#10ac84">
                <i class="fas fa-plus"></i> إضافة مشرف
            </a>
            <a href="{{ route('admins_trashed') }}" class="btn btn-danger btn-custom-header text-white" style="background:#ee5253">
                <i class="fas fa-trash"></i> المحذوفين
            </a>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2">
                <input class="form-control" id="searchAdmins" style="width:260px" placeholder="🔍 بحث عن مشرف...">
                <select class="form-select" id="filterStatus" style="width:150px">
                    <option value="all">جميع الحالات</option>
                    <option value="active">فعال</option>
                    <option value="inactive">غير فعال</option>
                    <option value="pending">قيد التنفيذ</option>
                    <option value="blocked">محظور</option>
                </select>
            </div>

            <div>
                عدد المشرفين: <strong>{{ $admins->total() }}</strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 15px">#</th>
                        <th class="text-center">الصورة</th>
                        <th class="text-right">الاسم</th>
                        <th class="text-center">اسم المستخدم</th>
                        <th class="text-center">البريد الإلكتروني</th>
                        <th class="text-center">الموبايل</th>
                        <th class="text-center">تاريخ الميلاد</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الجنس</th>
                        <th class="text-center actions-column">إجراءات</th>
                    </tr>
                </thead>

                <tbody id="adminsTableBody">
                    @forelse($admins as $admin)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($admin->image)
                            <img src="{{ asset('storage/images/admin/'.$admin->image) }}" class="admins-avatar" alt="{{ $admin->name }}" width="42" height="42" loading="lazy" decoding="async">
                            @else
                            <span class="avatar-placeholder">
                                {{ mb_substr($admin->name, 0, 1, 'UTF-8') }}
                            </span>
                            @endif
                        </td>
                        <td><strong>{{ $admin->name }}</strong></td>
                        <td>{{ $admin->user_name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td dir="ltr">{{ $admin->mobile }}</td>
                        <td>{{ $admin->date ?? "---" }}</td>
                        <td>
                            <x-cms.status-badge :status="$admin->status ?? 'unknown'" />
                            @if($admin->status == 'active')
                            <x-cms.status-badge :status="$admin->status" /><span class="badge bg-success badge-status legacy-user-status">✅ فعال</span>
                            @elseif($admin->status == 'inactive')
                            <span class="badge bg-danger badge-status legacy-user-status">❌ غير فعال</span>
                            @elseif($admin->status == 'pending')
                            <span class="badge bg-warning badge-status">⏳ قيد التنفيذ</span>
                            @elseif($admin->status == 'blocked')
                            <span class="badge bg-dark badge-status legacy-user-status">🚫 محظور</span>
                            @endif
                        </td>
                        <td>
                            @if($admin->gender == 'male')
                            ذكر
                            @else
                            انثى
                            @endif
                        </td>
                        <td class="text-center actions-column">
                            <x-cms.table-actions :show-url="route('admins.show', $admin->id)" :edit-url="route('admins.edit', $admin->id)" :delete-url="route('admins.destroy', $admin->id)" show-permission="admins.view" edit-permission="admins.update" delete-permission="admins.delete" :delete-id="$admin->id" /><div class="action-btns legacy-user-actions">
                                <a href="{{ route('admins.show', $admin->id) }}" class="action-btn btn-view" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admins.edit', $admin->id) }}" class="action-btn btn-edit" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admins.destroy', $admin->id) }}" method="POST" class="d-inline" data-crud-delete data-confirm-message="لا يمكن التراجع عن حذف هذا الحساب.">
                                    @csrf
                                    @method('DELETE')
                                    <button class="action-btn btn-delete" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block" style="color: #ccc;"></i>
                            لا يوجد مشرفين مسجلين حالياً
                            <br>
                            <a href="{{ route('admins.create') }}" class="btn btn-sm mt-3 text-white" style="background-color: #10ac84; border-radius: 8px;">
                                <i class="fas fa-plus"></i> أضف أول مشرف
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $admins->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // حذف المشرف

    // فلترة وبحث الجدول
    function filterTable() {
        const searchText = document.getElementById('searchAdmins').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('#adminsTableBody tr:not(#noResultsMessage):not(#emptyRow)');
        let visibleCount = 0;

        rows.forEach(row => {
            // توذيع الأعمدة حسب ترتيبها الصحيح في <tr>
            const name = row.querySelector('td:nth-child(3)')?.textContent?.toLowerCase() || '';
            const userName = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
            const email = row.querySelector('td:nth-child(5)')?.textContent?.toLowerCase() || '';
            const phone = row.querySelector('td:nth-child(6)')?.textContent?.toLowerCase() || '';
            const statusBadgeText = row.querySelector('td:nth-child(8)')?.textContent?.trim() || '';

            // تحويل نص حالة البادج إلى المفتاح المماثل للـ Select
            let status = '';
            if (statusBadgeText.includes('فعال') && !statusBadgeText.includes('غير')) status = 'active';
            else if (statusBadgeText.includes('غير فعال')) status = 'inactive';
            else if (statusBadgeText.includes('قيد التنفيذ')) status = 'pending';
            else if (statusBadgeText.includes('محظور')) status = 'blocked';

            // مطابقة البحث النصي (الاسم، اسم المستخدم، الايميل، الهاتف)
            let matchesSearch = true;
            if (searchText) {
                matchesSearch = name.includes(searchText) ||
                    userName.includes(searchText) ||
                    email.includes(searchText) ||
                    phone.includes(searchText);
            }

            // مطابقة الحالة
            let matchesStatus = true;
            if (statusFilter !== 'all') {
                matchesStatus = (status === statusFilter);
            }

            // تطبيق الإظهار/الإخفاء
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // رسالة عدم وجود نتائج
        const noResults = document.getElementById('noResultsMessage');
        if (visibleCount === 0 && rows.length > 0) {
            if (!noResults) {
                const tbody = document.getElementById('adminsTableBody');
                const tr = document.createElement('tr');
                tr.id = 'noResultsMessage';
                tr.innerHTML = `
                    <td colspan="10" class="text-center py-4 text-muted">
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

    // ربط الأحداث فور تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchAdmins');
        const statusSelect = document.getElementById('filterStatus');

        // التصفية أثناء الكتابة مباشرة
        searchInput?.addEventListener('input', filterTable);
        // التصفية عند تغيير الحالة
        statusSelect?.addEventListener('change', filterTable);
    });
</script>
@endsection
