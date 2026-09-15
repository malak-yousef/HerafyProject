@extends('cms.parent')

@section('title', 'العروض')
@section('main_content', 'العروض')

@section('styles')
<style>
    .offer-order-title { max-width: 260px; }
    .offer-order-title > span { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .offers-table .badge { white-space: nowrap; }
    .offers-table .cms-table-actions { min-width: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fa-solid fa-hand-holding-dollar text-warning" aria-hidden="true"></i> العروض</h4>
            <p class="text-muted mb-0">إدارة عروض الحرفيين على طلبات الخدمات.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('offers.create', 'admin')
                <a href="{{ route('offers.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus" aria-hidden="true"></i> إضافة عرض</a>
            @endcan
            @can('offers.view', 'admin')
                <a href="{{ route('offers_trashed') }}" class="btn btn-outline-danger"><i class="fa-solid fa-trash-can" aria-hidden="true"></i> سلة المحذوفات</a>
            @endcan
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('offers.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-lg-3 col-md-6"><label for="offer-search" class="form-label">البحث</label><input id="offer-search" name="search" value="{{ request('search') }}" class="form-control" maxlength="100" placeholder="الطلب أو اسم الحرفي"></div>
                <div class="col-lg-2 col-md-6"><label for="offer-status" class="form-label">الحالة</label><select id="offer-status" name="status" class="form-select"><option value="">كل الحالات</option>@foreach(['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="offer-craftman" class="form-label">الحرفي</label><select id="offer-craftman" name="craftman_id" class="form-select"><option value="">كل الحرفيين</option>@foreach($craftmen as $craftman)<option value="{{ $craftman->id }}" @selected((string) request('craftman_id') === (string) $craftman->id)>{{ $craftman->user?->name ?? 'غير متاح' }}</option>@endforeach</select></div>
                <div class="col-lg-3 col-md-6"><label for="offer-order" class="form-label">الطلب</label><select id="offer-order" name="order_id" class="form-select"><option value="">كل الطلبات</option>@foreach($orders as $order)<option value="{{ $order->id }}" @selected((string) request('order_id') === (string) $order->id)>{{ $order->title }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="offer-price-min" class="form-label">السعر من</label><input id="offer-price-min" type="number" step="0.01" min="0" name="price_min" value="{{ request('price_min') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="offer-price-max" class="form-label">السعر إلى</label><input id="offer-price-max" type="number" step="0.01" min="0" name="price_max" value="{{ request('price_max') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="offer-date-from" class="form-label">تاريخ التقديم من</label><input id="offer-date-from" type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="offer-date-to" class="form-label">تاريخ التقديم إلى</label><input id="offer-date-to" type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
                <div class="col-lg-2 col-md-6"><label for="offer-sort" class="form-label">الترتيب</label><select id="offer-sort" name="sort" class="form-select">@foreach(['newest' => 'الأحدث', 'oldest' => 'الأقدم', 'price_asc' => 'السعر: الأقل', 'price_desc' => 'السعر: الأعلى'] as $value => $label)<option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-lg-3 col-md-6 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> تطبيق</button><a class="btn btn-outline-secondary" href="{{ route('offers.index') }}">إعادة تعيين</a></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center offers-table">
                    <thead class="table-light"><tr><th>الطلب</th><th>الحرفي</th><th>السعر المقترح</th><th>الحالة</th><th>تاريخ التقديم</th><th>الإجراءات</th></tr></thead>
                    <tbody>
                        @forelse($offers as $offer)
                            <tr>
                                <td class="offer-order-title" title="{{ $offer->order?->title }}"><span>{{ $offer->order?->title ?? 'غير متاح' }}</span></td>
                                <td>{{ $offer->craftman?->user?->name ?? 'غير متاح' }}</td>
                                <td>{{ number_format((float) $offer->price, 2) }} ₪</td>
                                <td><x-cms.status-badge :status="$offer->status" /></td>
                                <td>{{ $offer->created_at?->format('Y-m-d') ?? '—' }}</td>
                                <td><x-cms.table-actions :show-url="route('offers.show', $offer->id)" :edit-url="route('offers.edit', $offer->id)" :delete-url="route('offers.destroy', $offer->id)" show-permission="offers.view" edit-permission="offers.update" delete-permission="offers.delete" :delete-id="$offer->id" /></td>
                            </tr>
                        @empty
                            @php($hasFilters = request()->hasAny(['search', 'status', 'craftman_id', 'order_id', 'price_min', 'price_max', 'date_from', 'date_to', 'sort']))
                            <tr><td colspan="6"><x-cms.empty-state :title="$hasFilters ? 'لا توجد نتائج مطابقة لخيارات البحث.' : 'لا توجد عروض حتى الآن.'" :reset-url="$hasFilters ? route('offers.index') : null" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $offers->links() }}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/offers/' + id, reference);
    }
</script>
@endsection
