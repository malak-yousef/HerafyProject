@extends('cms.parent')

@section('title', 'الطلبات')
@section('main_content', 'الطلبات')

@section('styles')
<style>
    .order-title-cell { max-width: 240px; }
    .order-title-cell > span { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .orders-table .badge { white-space: nowrap; }
    .orders-table .cms-table-actions { min-width: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-clipboard-list text-warning" aria-hidden="true"></i> الطلبات</h4>
            <p class="text-muted mb-0">إدارة طلبات الخدمات ومراجعة بياناتها.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('orders.create', 'admin')
                <a href="{{ route('orders.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus" aria-hidden="true"></i> إضافة طلب</a>
            @endcan
            @can('orders.view', 'admin')
                <a href="{{ route('orders_trashed') }}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> سلة المحذوفات</a>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-lg-3 col-md-6"><label for="order-search" class="form-label">البحث</label><input id="order-search" name="search" value="{{ request('search') }}" class="form-control" maxlength="100" placeholder="العنوان أو اسم العميل"></div>
                <div class="col-lg-2 col-md-6">
                    <label for="order-status" class="form-label">الحالة</label>
                    <select id="order-status" name="status" class="form-select"><option value="">الكل</option>
                        @foreach(['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6"><label for="order-profession" class="form-label">المهنة</label><select id="order-profession" name="profession_id" class="form-select"><option value="">الكل</option>@foreach($professions as $profession)<option value="{{ $profession->id }}" @selected((string) request('profession_id') === (string) $profession->id)>{{ $profession->name }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="order-city" class="form-label">المدينة</label><select id="order-city" name="city_id" class="form-select"><option value="">كل المدن</option>@foreach($cities as $city)<option value="{{ $city->id }}" @selected((string) request('city_id') === (string) $city->id)>{{ $city->name }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="order-area" class="form-label">المنطقة</label><select id="order-area" name="area_id" class="form-select"><option value="">كل المناطق</option>@foreach($areas as $area)<option value="{{ $area->id }}" data-city="{{ $area->city_id }}" @selected((string) request('area_id') === (string) $area->id)>{{ $area->name }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="order-date-from" class="form-label">تاريخ الإنشاء من</label><input id="order-date-from" type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="order-date-to" class="form-label">تاريخ الإنشاء إلى</label><input id="order-date-to" type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="service-date-from" class="form-label">تاريخ الخدمة من</label><input id="service-date-from" type="date" name="service_date_from" value="{{ request('service_date_from') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="service-date-to" class="form-label">تاريخ الخدمة إلى</label><input id="service-date-to" type="date" name="service_date_to" value="{{ request('service_date_to') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6">
                    <label for="order-sort" class="form-label">الترتيب</label>
                    <select id="order-sort" name="sort" class="form-select">
                        @foreach(['newest' => 'الأحدث', 'oldest' => 'الأقدم', 'service_date_asc' => 'تاريخ الخدمة: الأقرب', 'service_date_desc' => 'تاريخ الخدمة: الأبعد', 'budget_asc' => 'الميزانية: الأقل', 'budget_desc' => 'الميزانية: الأعلى'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> تطبيق</button><a class="btn btn-outline-secondary" href="{{ route('orders.index') }}">إعادة تعيين</a></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center orders-table">
                    <thead class="table-light"><tr><th>العميل</th><th>عنوان الطلب</th><th>المهنة</th><th>المدينة / المنطقة</th><th>الميزانية</th><th>تاريخ الخدمة</th><th>الحالة</th><th>تاريخ الإنشاء</th><th>الإجراءات</th></tr></thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->customer?->user?->name ?? 'غير متاح' }}</td>
                                <td class="order-title-cell" title="{{ $order->title }}"><span>{{ $order->title }}</span></td>
                                <td>{{ $order->profession?->name ?? 'غير متاح' }}</td>
                                <td>{{ $order->city?->name ?? 'غير متاح' }} <span class="text-muted">/</span> {{ $order->area?->name ?? 'غير متاح' }}</td>
                                <td>{{ $order->budget !== null ? number_format((float) $order->budget, 2) : 'غير محددة' }}</td>
                                <td>{{ $order->service_date?->format('Y-m-d') ?? 'غير محدد' }}</td>
                                <td><x-cms.status-badge :status="$order->status" /></td>
                                <td>{{ $order->created_at?->format('Y-m-d') ?? 'غير متاح' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @can('offers.create', 'admin')
                                            <a href="{{ route('offers.create', ['order_id' => $order->id]) }}" class="btn btn-sm btn-success" title="إضافة عرض">
                                                <i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
                                                إضافة عرض
                                            </a>
                                        @endcan
                                        <x-cms.table-actions :show-url="route('orders.show', $order->id)" :edit-url="route('orders.edit', $order->id)" :delete-url="route('orders.destroy', $order->id)" show-permission="orders.view" edit-permission="orders.update" delete-permission="orders.delete" :delete-id="$order->id" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            @php
                                $hasFilters = request()->hasAny(['search', 'status', 'profession_id', 'city_id', 'area_id', 'date_from', 'date_to', 'service_date_from', 'service_date_to', 'sort']);
                            @endphp
                            <tr><td colspan="9"><x-cms.empty-state :title="$hasFilters ? 'لا توجد نتائج مطابقة لخيارات البحث.' : 'لا توجد طلبات حتى الآن.'" :reset-url="$hasFilters ? route('orders.index') : null" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('cms/js/order-location.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initCmsOrderLocation('#order-city', '#order-area');
    });

    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/orders/' + id, reference);
    }
</script>
@endsection
