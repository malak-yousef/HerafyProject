@extends('herafy.parent')

@section('title', 'الرئيسية')

@section('style')
<style>
    .customer-home { background: #f8f9fa; min-height: 65vh; }
    .welcome-card { background: linear-gradient(135deg, #2d2d2d, #111); color: #fff; border-radius: 22px; }
    .quick-action, .summary-card { border: 0; border-radius: 16px; transition: transform .2s ease, box-shadow .2s ease; }
    .quick-action:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08); }
    .quick-icon { width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; background: #fff3cd; color: #8a6200; border-radius: 14px; }
    .status-badge { font-size: .78rem; }
</style>
@endsection

@section('content')
    @if($customer)
        <main class="customer-home py-5">
            <div class="container">
                <section class="welcome-card p-4 p-lg-5 mb-4 shadow-sm">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <span class="text-warning fw-bold">لوحة العميل</span>
                            <h1 class="h2 fw-bold mt-2">مرحباً، {{ $customer->user->name }}</h1>
                            <p class="text-white-50 mb-0">ابدأ طلب خدمة جديد أو تابع حالة طلباتك الحالية بسهولة.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="{{ route('request') }}" class="btn btn-warning fw-bold px-4 py-2">
                                <i class="fas fa-plus-circle ms-1"></i> طلب خدمة جديد
                            </a>
                        </div>
                    </div>
                </section>

                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="card summary-card h-100 shadow-sm"><div class="card-body"><span class="text-muted">طلبات قيد الانتظار</span><div class="display-6 fw-bold text-warning">{{ $orderSummary['pending'] }}</div></div></div></div>
                    <div class="col-md-4"><div class="card summary-card h-100 shadow-sm"><div class="card-body"><span class="text-muted">مقبولة / قيد التنفيذ</span><div class="display-6 fw-bold text-primary">{{ $orderSummary['active'] }}</div></div></div></div>
                    <div class="col-md-4"><div class="card summary-card h-100 shadow-sm"><div class="card-body"><span class="text-muted">طلبات مكتملة</span><div class="display-6 fw-bold text-success">{{ $orderSummary['completed'] }}</div></div></div></div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-md-4"><a class="card quick-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('request') }}"><div class="card-body d-flex align-items-center gap-3"><span class="quick-icon"><i class="fas fa-plus"></i></span><div><h2 class="h6 fw-bold mb-1">طلب خدمة</h2><small class="text-muted">أنشئ طلباً جديداً</small></div></div></a></div>
                    <div class="col-md-4"><a class="card quick-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.customer.orders.index') }}"><div class="card-body d-flex align-items-center gap-3"><span class="quick-icon"><i class="fas fa-clipboard-list"></i></span><div><h2 class="h6 fw-bold mb-1">طلباتي</h2><small class="text-muted">تابع الطلبات والعروض</small></div></div></a></div>
                    <div class="col-md-4"><a class="card quick-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('showCraftsmen') }}"><div class="card-body d-flex align-items-center gap-3"><span class="quick-icon"><i class="fas fa-users"></i></span><div><h2 class="h6 fw-bold mb-1">تصفح الحرفيين</h2><small class="text-muted">اكتشف الحرفيين المتاحين</small></div></div></a></div>
                </div>

                <section class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                        <h2 class="h5 fw-bold mb-0">أحدث الطلبات</h2>
                        @if($recentOrders->isNotEmpty())<a href="{{ route('herafy.customer.orders.index') }}" class="text-warning fw-bold text-decoration-none">عرض الكل</a>@endif
                    </div>
                    <div class="card-body pt-0">
                        @forelse($recentOrders as $order)
                            <a href="{{ route('herafy.customer.orders.show', $order) }}" class="d-flex flex-column flex-md-row justify-content-between gap-2 py-3 border-top text-decoration-none text-dark">
                                <div><strong>{{ $order->title }}</strong><div class="small text-muted mt-1">{{ $order->profession?->name }} @if($order->city) · {{ $order->city->name }} @endif</div></div>
                                <div class="d-flex align-items-center gap-3"><span class="badge {{ \App\Support\StatusLabel::solidBadgeClass($order->status) }}">{{ \App\Support\StatusLabel::label($order->status) }}</span><small class="text-muted">{{ $order->created_at->format('Y-m-d') }}</small></div>
                            </a>
                        @empty
                            <div class="text-center py-5"><i class="fas fa-clipboard fa-3x text-warning mb-3"></i><h3 class="h5 fw-bold">لا توجد طلبات بعد</h3><p class="text-muted">أنشئ أول طلب خدمة وسيظهر هنا.</p><a href="{{ route('request') }}" class="btn btn-warning">طلب خدمة</a></div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    @endif
@endsection
