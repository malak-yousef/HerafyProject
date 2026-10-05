@extends('herafy.parent')

@section('title', 'طلباتي')

@section('content')
<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-clipboard-list text-warning"></i> طلباتي</h2>
                <p class="text-muted mb-0">تابع طلبات الخدمات التي أرسلتها.</p>
            </div>
            <a href="{{ route('request') }}" class="btn btn-warning fw-bold">
                <i class="fas fa-plus"></i> طلب خدمة جديدة
            </a>
        </div>

        <div class="row g-4">
            @forelse ($orders as $order)
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3 mb-3">
                                <h5 class="fw-bold mb-0">{{ $order->title }}</h5>
                                <x-herafy.status-badge :status="$order->status" />
                            </div>

                            <div class="row g-2 text-muted small">
                                <div class="col-sm-6"><i class="fas fa-tools text-warning"></i> {{ $order->profession->name ?? '-' }}</div>
                                <div class="col-sm-6"><i class="fas fa-location-dot text-warning"></i> {{ $order->city->name ?? '-' }} / {{ $order->area->name ?? '-' }}</div>
                                <div class="col-sm-6"><i class="fas fa-money-bill text-warning"></i> <x-money :value="$order->budget" /></div>
                                <div class="col-sm-6"><i class="fas fa-calendar text-warning"></i> {{ $order->service_date?->format('Y-m-d') ?? '-' }}</div>
                                <div class="col-sm-6"><i class="fas fa-image text-warning"></i> {{ $order->images_count }} صورة</div>
                                <div class="col-sm-6"><i class="fas fa-clock text-warning"></i> {{ $order->created_at->format('Y-m-d') }}</div>
                            </div>

                            <a href="{{ route('herafy.customer.orders.show', $order) }}" class="btn btn-outline-warning w-100 mt-4">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">لم ترسل أي طلبات بعد.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</section>
@endsection
