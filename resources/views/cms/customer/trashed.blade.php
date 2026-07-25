@extends('cms.parent')

@section('title', 'الزبائن')

@section('styles')
<style>
    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #FFC107;
    }

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

    .action-btn.btn-toggle {
        background: #0984e3;
    }

    .action-btn.btn-toggle:hover {
        background: #0770c4;
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
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

        .customer-avatar {
            width: 30px;
            height: 30px;
        }
    }
</style>
@endsection

@section('main_content', 'الزبائن')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">👥 إدارة الزبائن</h4>
            <p class="text-muted small">عرض وإدارة جميع الزبائن المحذوفين</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customers.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> إضافة زبون
            </a>

            <a class="btn btn-warning btn-sm" href="{{ route('customers.index') }}">
                <i class="fas fa-arrow-left"></i> عودة
            </a>

            <button class="btn btn-danger btn-sm" onclick="performDestroy()">
                <i class="fas fa-download"></i> حذف الكل
            </button>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" class="form-control form-control-sm" id="searchCustomers"
                    placeholder="🔍 بحث عن زبون..." oninput="filterTable()">
                <select class="form-select form-select-sm" id="filterStatus" onchange="filterTable()" style="width: 150px;">
                    <option value="all">جميع الحالات</option>
                    <option value="active">فعال</option>
                    <option value="inactive">غير فعال</option>
                    <option value="pending">قيد التنفيذ</option>
                    <option value="blocked">محظور</option>
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
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>الجنس</th>
                        <th style="width: 90px;">الحالة</th>
                        <th style="width: 120px;">تاريخ الإضافة</th>
                        <th style="width: 180px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="customersTableBody">
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($customer->user && $customer->user->image)
                            <img src="{{ asset('storage/images/customer/' . $customer->user->image) }}" alt="{{ $customer->user->name }}" class="customer-avatar">
                            @else
                            <span class="avatar-placeholder">
                                {{ $customer->user ? substr($customer->user->name, 0, 1) : '?' }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $customer->user->name ?? 'غير محدد' }}</strong>
                        </td>
                        <td>{{ $customer->user->email ?? 'لا يوجد بريد' }}</td>
                        <td>{{ $customer->user->mobile ?? '-' }}</td>
                        <td>
                            @if($customer->user && $customer->user->gender == 'male')
                            <span class="badge bg-info text-white">ذكر</span>
                            @elseif($customer->user && $customer->user->gender == 'female')
                            <span class="badge bg-pink text-white">أنثى</span>
                            @else
                            <span class="badge bg-secondary">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if($customer->user && $customer->user->status == 'active')
                            <span class="badge bg-success badge-status">✅ فعال</span>
                            @elseif($customer->user && $customer->user->status == 'inactive')
                            <span class="badge bg-danger badge-status">❌ غير فعال</span>
                            @elseif($customer->user && $customer->user->status == 'pending')
                            <span class="badge bg-warning text-dark badge-status">⏳ قيد التنفيذ</span>
                            @elseif($customer->user && $customer->user->status == 'blocked')
                            <span class="badge bg-dark badge-status">🚫 محظور</span>
                            @endif
                        </td>
                        <td>{{ $customer->created_at ? $customer->created_at->format('Y-m-d') : '-' }}</td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('customers.show', $customer->id) }}" class="action-btn btn-view" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- اعادة --}}
                                <a href="{{ route('customers_restore' , $customer->id) }}" class="action-btn btn-edit"
                                    title="اعادة">
                                    <i class="fas fa-undo"></i>
                                </a>

                                <button class="action-btn btn-delete" onclick="performDestroy('{{ $customer->id }}', this)" title="حذف">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                            لا يوجد زبائن مسجلين حالياً
                            <br>
                            <a href="{{ route('customers.create') }}" class="btn btn-warning btn-sm mt-2">
                                <i class="fas fa-plus"></i> أضف أول زبون
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
    // 2. حذف الزبون
    // ============================================
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/customers/' + id, reference);
    }

    // ============================================
    // 3. فلترة الجدول
    // ============================================
    function filterTable() {
        const searchText = document.getElementById('searchCustomers').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('#customersTableBody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(3) strong')?.textContent?.toLowerCase() || '';
            const email = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
            const phone = row.querySelector('td:nth-child(5)')?.textContent?.toLowerCase() || '';
            const statusBadge = row.querySelector('td:nth-child(7) .badge')?.textContent?.trim() || '';

            let isActive = statusBadge.includes('فعال') || statusBadge.includes('قيد التنفيذ');
            let isBlocked = statusBadge.includes('محظور');

            let status = 'active';
            if (isBlocked) status = 'blocked';
            else if (statusBadge.includes('غير فعال')) status = 'inactive';
            else if (statusBadge.includes('قيد التنفيذ')) status = 'pending';

            let show = true;

            if (searchText && !name.includes(searchText) && !email.includes(searchText) && !phone.includes(searchText)) {
                show = false;
            }

            if (statusFilter !== 'all' && status !== statusFilter) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        const noResults = document.getElementById('noResultsMessage');
        if (visibleCount === 0) {
            if (!noResults) {
                const tbody = document.getElementById('customersTableBody');
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
            if (noResults) noResults.remove();
        }
    }

    // ============================================


    // ============================================
    // 5. إضافة مستمع للأحداث
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchCustomers')?.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterTable();
            }
        });

        console.log('✅ صفحة الزبائن جاهزة');
    });
</script>
@endsection