@extends('cms.parent')

@section('title', 'مهام الخدمات')
@section('main_content', 'مهام الخدمات')

@section('styles')
<style>
    .service-job-order-title { max-width: 240px; }
    .service-job-order-title > span { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .service-jobs-table .badge, .service-job-timestamp { white-space: nowrap; }
    .service-jobs-table .cms-table-actions { min-width: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-briefcase text-warning" aria-hidden="true"></i> مهام الخدمات</h4>
            <p class="text-muted mb-0">متابعة مهام الخدمات المرتبطة بالطلبات والعروض.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('service-jobs.create', 'admin')
                <a href="{{ route('service-jobs.create') }}" class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> إضافة خدمة</a>
            @endcan
            @can('service-jobs.view', 'admin')
                <a href="{{ route('service-jobs.trashed') }}" class="btn btn-outline-danger"><i class="fas fa-trash-can" aria-hidden="true"></i> المحذوفات</a>
            @endcan
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success"><i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}</div>@endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" action="{{ route('service-jobs.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-lg-3 col-md-6"><label for="job-search" class="form-label">البحث</label><input id="job-search" name="search" value="{{ request('search') }}" class="form-control" maxlength="100" placeholder="الطلب أو العميل أو الحرفي"></div>
                <div class="col-lg-2 col-md-6"><label for="job-status" class="form-label">الحالة</label><select id="job-status" name="status" class="form-select"><option value="">كل الحالات</option>@foreach(['pending' => 'قيد الانتظار', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="job-customer" class="form-label">العميل</label><select id="job-customer" name="customer_id" class="form-select"><option value="">كل العملاء</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->user?->name ?? 'غير متاح' }}</option>@endforeach</select></div>
                <div class="col-lg-2 col-md-6"><label for="job-craftman" class="form-label">الحرفي</label><select id="job-craftman" name="craftman_id" class="form-select"><option value="">كل الحرفيين</option>@foreach($craftsmen as $craftman)<option value="{{ $craftman->id }}" @selected((string) request('craftman_id') === (string) $craftman->id)>{{ $craftman->user?->name ?? 'غير متاح' }}</option>@endforeach</select></div>
                <div class="col-lg-3 col-md-6"><label for="job-order" class="form-label">الطلب</label><select id="job-order" name="order_id" class="form-select"><option value="">كل الطلبات</option>@foreach($orders as $order)<option value="{{ $order->id }}" @selected((string) request('order_id') === (string) $order->id)>{{ $order->title }}</option>@endforeach</select></div>
                @foreach([['date_from', 'تاريخ الإنشاء من'], ['date_to', 'تاريخ الإنشاء إلى'], ['started_from', 'تاريخ البدء من'], ['started_to', 'تاريخ البدء إلى'], ['completed_from', 'تاريخ الإنجاز من'], ['completed_to', 'تاريخ الإنجاز إلى']] as [$name, $label])
                    <div class="col-lg-2 col-md-6"><label for="job-{{ $name }}" class="form-label">{{ $label }}</label><input id="job-{{ $name }}" type="date" name="{{ $name }}" value="{{ request($name) }}" class="form-control"></div>
                @endforeach
                <div class="col-lg-2 col-md-6"><label for="job-sort" class="form-label">الترتيب</label><select id="job-sort" name="sort" class="form-select">@foreach(['newest' => 'الأحدث', 'oldest' => 'الأقدم', 'started_desc' => 'البدء: الأحدث', 'started_asc' => 'البدء: الأقدم', 'completed_desc' => 'الإنجاز: الأحدث', 'completed_asc' => 'الإنجاز: الأقدم'] as $value => $label)<option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-lg-3 col-md-6 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> تطبيق</button><a class="btn btn-outline-secondary" href="{{ route('service-jobs.index') }}">إعادة تعيين</a></div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center service-jobs-table">
                    <thead class="table-light"><tr><th>الطلب</th><th>العميل</th><th>الحرفي</th><th>الحالة</th><th>تاريخ البدء</th><th>تاريخ الإنجاز</th><th>آخر تحديث</th><th>الإجراءات</th></tr></thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td class="service-job-order-title" title="{{ $job->order?->title }}"><span>{{ $job->order?->title ?? 'غير متاح' }}</span></td>
                                <td>{{ $job->customer?->user?->name ?? 'غير متاح' }}</td>
                                <td>{{ $job->craftman?->user?->name ?? 'غير متاح' }}</td>
                                <td><x-cms.status-badge :status="$job->status" /></td>
                                <td class="service-job-timestamp">{{ $job->started_at?->format('Y-m-d H:i') ?? 'لم يبدأ بعد' }}</td>
                                <td class="service-job-timestamp">{{ $job->completed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="service-job-timestamp">{{ $job->updated_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td><x-cms.table-actions :show-url="route('service-jobs.show', $job)" :edit-url="route('service-jobs.edit', $job)" :delete-url="route('service-jobs.destroy', $job)" show-permission="service-jobs.view" edit-permission="service-jobs.update" delete-permission="service-jobs.delete" :delete-id="$job->id" /></td>
                            </tr>
                        @empty
                            @php($hasFilters = request()->hasAny(['search', 'status', 'customer_id', 'craftman_id', 'order_id', 'date_from', 'date_to', 'started_from', 'started_to', 'completed_from', 'completed_to', 'sort']))
                            <tr><td colspan="8"><x-cms.empty-state :title="$hasFilters ? 'لا توجد نتائج مطابقة لخيارات البحث.' : 'لا توجد مهام خدمات حتى الآن.'" :reset-url="$hasFilters ? route('service-jobs.index') : null" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $jobs->links() }}</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/service-jobs/' + id, reference);
    }
</script>
@endsection
