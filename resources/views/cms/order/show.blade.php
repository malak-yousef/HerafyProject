@extends('cms.parent')

@section('title', 'تفاصيل الطلب')
@section('main_content', 'تفاصيل الطلب')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0"><i class="fa-solid fa-clipboard-list text-warning" aria-hidden="true"></i> طلب #{{ $order->id }} — {{ $order->title }}</h5>
            <div class="d-flex flex-wrap gap-2">
                @can('orders.update', 'admin')
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen" aria-hidden="true"></i> تعديل</a>
                @endcan
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-list" aria-hidden="true"></i> العودة للقائمة</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3">بيانات الطلب</h6>
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr><th scope="row" class="w-25">العميل</th><td>{{ $order->customer?->user?->name ?? 'غير متاح' }}</td></tr>
                            <tr><th scope="row">المهنة</th><td>{{ $order->profession?->name ?? 'غير متاح' }}</td></tr>
                            <tr><th scope="row">المدينة</th><td>{{ $order->city?->name ?? 'غير متاح' }}</td></tr>
                            <tr><th scope="row">المنطقة</th><td>{{ $order->area?->name ?? 'غير متاح' }}</td></tr>
                            <tr><th scope="row">العنوان</th><td>{{ $order->address ?? '-' }}</td></tr>
                            <tr><th scope="row">الهاتف</th><td>{{ $order->phone ?? '-' }}</td></tr>
                            <tr><th scope="row">الميزانية</th><td><x-money :value="$order->budget" placeholder="غير محددة" /></td></tr>
                            <tr><th scope="row">تاريخ الخدمة</th><td>{{ $order->service_date?->format('Y-m-d') ?? 'غير محدد' }}</td></tr>
                            <tr><th scope="row">الحالة</th><td><x-cms.status-badge :status="$order->status" /></td></tr>
                            <tr><th scope="row">تاريخ الإنشاء</th><td>{{ $order->created_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-6">
                    <h6 class="fw-bold mb-3">تفاصيل الطلب</h6>
                    <p class="mb-0 text-break">{{ $order->description ?? '-' }}</p>

                    <h6 class="fw-bold mb-3 mt-4">صور الطلب</h6>
                    @if ($order->images->isNotEmpty())
                        <div class="row g-2">
                            @foreach ($order->images as $image)
                                <div class="col-4 col-md-3">
                                    <a href="{{ $image->publicUrl() }}" target="_blank" rel="noopener">
                                        <img src="{{ $image->publicUrl() }}" alt="صورة مرفقة بالطلب {{ $order->title }}" class="img-fluid rounded border" style="height: 90px; width: 100%; object-fit: cover;" width="90" height="90" loading="lazy" decoding="async">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">لا توجد صور مرفقة.</p>
                    @endif

                    <h6 class="fw-bold mb-3 mt-4">العمل المرتبط</h6>
                    @if ($order->serviceJob)
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr><th scope="row" class="w-25">الحرفي</th><td>{{ $order->serviceJob->craftman?->user?->name ?? 'غير متاح' }}</td></tr>
                                <tr><th scope="row">حالة العمل</th><td><x-cms.status-badge :status="$order->serviceJob->status" /></td></tr>
                                <tr><th scope="row">وقت البدء</th><td>{{ $order->serviceJob->started_at?->format('Y-m-d H:i') ?? '-' }}</td></tr>
                                <tr><th scope="row">وقت الإكمال</th><td>{{ $order->serviceJob->completed_at?->format('Y-m-d H:i') ?? '-' }}</td></tr>
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted mb-0">لم يتم إنشاء عمل خدمة لهذا الطلب بعد.</p>
                    @endif
                </div>
            </div>

            <h6 class="fw-bold mb-3 mt-4">العروض المقدمة</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr><th>الحرفي</th><th>السعر</th><th>الحالة</th><th>ملاحظة</th><th>تاريخ الإنشاء</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($order->offers as $offer)
                            <tr>
                                <td>{{ $offer->craftman?->user?->name ?? 'غير متاح' }}</td>
                                <td><x-money :value="$offer->price" /></td>
                                <td><x-cms.status-badge :status="$offer->status" /></td>
                                <td class="text-start text-break">{{ $offer->message ?? '-' }}</td>
                                <td>{{ $offer->created_at?->format('Y-m-d') ?? 'غير متاح' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-cms.empty-state title="لا توجد عروض مقدمة على هذا الطلب بعد." icon="fa-hand-holding-dollar" /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection