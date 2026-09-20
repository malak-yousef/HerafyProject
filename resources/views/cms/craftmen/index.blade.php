@extends('cms.parent')

@section('title', 'الحرفيون')

@section('styles')
<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }

    /* تنسيق الصورة في الجدول */
    .craftsman-avatar {
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

    /* بادج اسم الحرفة الداخلي */
    .badge-profession {
        background-color: #f1f2f6 !important;
        color: #2f3542 !important;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
    }

    /* تنسيق أزرار الإجراءات */
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

    /*
    .action-btn.btn-toggle {
        background: #0984e3;
    }

    .action-btn.btn-toggle:hover {
        background: #0770c4;
    }*/



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

    /* شريط البحث والفلترة الاحترافي */
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
        /* محاذاة الرقم والاسم لليمين */
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

    /* تحسين شريط التمرير (Scrollbar) */
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

    /* أزرار الإضافة والتصدير العلوية */
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

@section('main_content', 'الحرفيون')

@section('content')
<div class="section-content">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2c3e50;">إدارة الحرفيين</h4>
            <p class="text-muted small mb-0">عرض وإدارة جميع الحرفيين المسجلين في المنصة</p>
        </div>
        <div class="d-flex gap-2 align-items-center">

            <a href="{{ route('craftmen.create') }}" class="btn btn-success btn-custom-header text-white" style="background-color: #10ac84;">
                <i class="fas fa-plus"></i> إضافة حرفي
            </a>
            <button class="btn btn-warning btn-custom-header text-dark" style="background-color: #f1c40f;" onclick="exportData('craftsmen')">
                <i class="fas fa-download"></i> تصدير
            </button>
            <a href="{{ route('craftmen_trashed') }}" class="btn btn-danger btn-custom-header text-white" style="background-color: #ee5253;">
                <i class="fas fa-trash-alt"></i> المحذوفين
            </a>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <div class="d-flex gap-2 flex-wrap">
            <input type="text" class="form-control" id="searchCraftsmen" style="width: 260px;"
                placeholder="🔍 بحث عن حرفي..." oninput="filterTable('craftsmen')">
            <select class="form-select " id="filterStatus" onchange="filterTable('craftsmen')"
                style="width: 150px;">
                <option value="all">جميع الحالات</option>
                <option value="active">فعال</option>
                <option value="inactive">غير فعال</option>
                <option value="pending">قيد التنفيذ</option>
                <option value="blocked">محظور</option>
            </select>
        </div>
        <div>
            <span class="text-muted small">عدد الحرفيين: <strong style="color: #2c3e50;">{{ $craftmen->total() }}</strong></span>
        </div>
    </div>

    <div class="table-responsive">
        <form method="GET" class="row g-2 mb-3 align-items-end"><div class="col-md-3"><label class="form-label">بحث</label><input name="search" value="{{ request('search') }}" class="form-control" maxlength="100"></div><div class="col-md-3"><label class="form-label">المهنة</label><select name="profession_id" class="form-select"><option value="">الكل</option>@foreach($professions as $profession)<option value="{{ $profession->id }}" @selected((string)request('profession_id') === (string)$profession->id)>{{ $profession->name }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label">الحالة</label><select name="account_status" class="form-select"><option value="">الكل</option>@foreach(['active','inactive','blocked'] as $status)<option value="{{ $status }}" @selected(request('account_status') === $status)>{{ $status }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label">التوثيق</label><select name="verification_status" class="form-select"><option value="">الكل</option>@foreach(['unverified','pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('verification_status') === $status)>{{ $status }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary" type="submit">بحث</button> <a href="{{ route('craftmen.index') }}">إعادة ضبط</a></div></form>
        <div class="mb-3"><label class="form-label">الترتيب</label><form method="GET" class="d-inline"><input type="hidden" name="search" value="{{ request('search') }}"><input type="hidden" name="profession_id" value="{{ request('profession_id') }}"><select name="sort" class="form-select d-inline-block w-auto" onchange="this.form.submit()"><option value="newest">الأحدث</option><option value="oldest">الأقدم</option><option value="name_asc">الاسم أ-ي</option><option value="name_desc">الاسم ي-أ</option><option value="experience_desc">الأكثر خبرة</option><option value="experience_asc">الأقل خبرة</option></select></form></div><table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 60px;">الصورة</th>
                    <th>الاسم</th>
                    {{-- <th>الحرفة</th> --}}
                    <th>البريد الالكتروني</th>
                    <th>اسم الحرفة</th>
                    <th style="width: 90px;">الخبرة</th>
                    <th style="width: 110px;">رقم الهاتف</th>

                    <th style="width: 120px;">العنوان</th>
                    <th style="width: 120px;">تاريخ الانضمام</th>
                    <th style="width: 110px;">الحالة</th>
                    <th style="width: 100px;">أبرز الحرفيين</th>
                    <th style="width: 140px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="craftsmenTableBody">
                @forelse($craftmen as $craftman)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($craftman->user && $craftman->user->image)
                        <img src="{{ $craftman->user->profilePhotoUrl() }}" alt="{{ $craftman->user->name }}" class="craftsman-avatar" width="42" height="42" loading="lazy" decoding="async">
                        @else
                        <span class="avatar-placeholder">
                            {{ $craftman->user ? substr($craftman->user->name, 0, 1) : '?' }}
                        </span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $craftman->user->name ?? 'غير محدد' }}</strong>
                    </td>

                    <td>
                        {{ $craftman->user->email ?? 'لا يوجد بريد' }}
                    </td>

                    <td>
                        <span class="badge badge-profession">
                            {{ $craftman->profession->name ?? '-' }}
                        </span>
                    </td>



                    <td>{{ $craftman->experience_years ?? 0 }} سنة</td>
                    <td dir="ltr">{{ $craftman->user->mobile ?? '-' }}</td>

                    <td>{{ $craftman->user->address ?? '-' }}</td>
                    <td>{{ $craftman->created_at ? $craftman->created_at->format('Y-m-d') : '-' }}</td>
                    <td>
                        <x-cms.status-badge :status="$craftman->user->status ?? 'unknown'" />
                        @if($craftman->user && $craftman->user->status == 'active')
                        <x-cms.status-badge :status="$craftman->user->status" /><span class="badge bg-success badge-status legacy-user-status">✅ فعال</span>
                        @elseif($craftman->user && $craftman->user->status == 'inactive')
                        <span class="badge bg-danger badge-status legacy-user-status">❌ غير فعال</span>
                        @elseif($craftman->user && $craftman->user->status == 'pending')
                        <span class="badge bg-warning text-dark badge-status">⏳ قيد التنفيذ</span>
                        @elseif($craftman->user && $craftman->user->status == 'blocked')
                        <span class="badge bg-dark badge-status legacy-user-status">🚫 محظور</span>
                        @else
                        <span class="badge bg-secondary badge-status">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        @if($craftman->is_featured)
                            <span class="badge bg-warning text-dark"><i class="fas fa-star" aria-hidden="true"></i> مميز</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <x-cms.table-actions :show-url="route('craftmen.show', $craftman->id)" :edit-url="route('craftmen.edit', $craftman->id)" :delete-url="route('craftmen.destroy', $craftman->id)" show-permission="craftmen.view" edit-permission="craftmen.update" delete-permission="craftmen.delete" :delete-id="$craftman->id" /><div class="action-btns legacy-user-actions">
                            {{-- عرض --}}
                            <a href="{{ route('craftmen.show', $craftman->id) }}" class="action-btn btn-view" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- تعديل --}}
                            <a href="{{ route('craftmen.edit', $craftman->id) }}" class="action-btn btn-edit" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- حذف --}}
                            <button class="action-btn btn-delete" onclick="performDestroy('{{ $craftman->id }}', this)" title="حذف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center py-5 text-muted">
                        <i class="fas fa-users-slash fa-2x mb-2 d-block" style="color: #ccc;"></i>
                        لا يوجد حرفيين مسجلين حالياً
                        <br>
                        <a href="{{ route('craftmen.create') }}" class="btn btn-sm mt-3 text-white" style="background-color: #10ac84; border-radius: 8px;">
                            <i class="fas fa-plus"></i> أضف أول حرفي
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($craftmen->hasPages())
    <div class="card-footer clearfix">
        {{ $craftmen->links() }}
    </div>
    @endif
</div>
</div>
@endsection

@section('scripts')
<script>
    // ============================================
    // 2. حذف الحرفي
    // ============================================
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/craftmen/' + id, reference);
    }

    // ============================================
    // 3. فلترة الجدول
    // ============================================
    function filterTable() {
        const searchText = document.getElementById('searchCraftsmen').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatus').value;
        const rows = document.querySelectorAll('#craftsmenTableBody tr:not(#noResultsMessage)');
        let visibleCount = 0;




        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(3) strong')?.textContent?.toLowerCase() || '';
            const email = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
            const craft = row.querySelector('td:nth-child(5) .badge-profession')?.textContent?.toLowerCase() || '';
            const phone = row.querySelector('td:nth-child(7)')?.textContent?.toLowerCase() || '';
            const statusBadge = row.querySelector('td:nth-child(10)')?.textContent?.trim() || '';

            let status = 'active';
            if (statusBadge.includes('محظور')) status = 'blocked';
            else if (statusBadge.includes('غير فعال')) status = 'inactive';
            else if (statusBadge.includes('قيد التنفيذ')) status = 'pending';

            let show = true;

            if (searchText && !name.includes(searchText) && !email.includes(searchText) && !craft.includes(searchText) && !phone.includes(searchText)) {
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
                const tbody = document.getElementById('craftsmenTableBody');
                const tr = document.createElement('tr');
                tr.id = 'noResultsMessage';
                tr.innerHTML = `
                    <td colspan="11" class="text-center py-4 text-muted">
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
    // Filtering is server-side; retain this legacy hook as a no-op for old markup.
    function filterTable() { return false; }
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchCraftsmen')?.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterTable();
            }
        });

        //   console.log('✅ صفحة الحرفيين جاهزة');
        //  console.log('📊 عدد الحرفيين: {{ $craftmen->total() }}');




    });
</script>
@endsection
