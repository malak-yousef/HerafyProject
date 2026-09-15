@extends('herafy.parent')

@section('title', 'لوحة تحكم الحرفي')

@section('style')
<style>
    .craftman-dashboard { background: #f8f9fa; min-height: 65vh; }
    .craftman-welcome { background: linear-gradient(135deg, #2d2d2d, #111); color: white; border-radius: 22px; }
    .dashboard-stat, .dashboard-action, .activity-card { border: 0; border-radius: 16px; }
    .dashboard-action { transition: transform .2s ease, box-shadow .2s ease; }
    .dashboard-action:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08); }
    .dashboard-icon { width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; background: #fff3cd; color: #8a6200; }
</style>
@endsection

@section('content')
@php
    $statusLabels = ['pending' => 'قيد الانتظار', 'accepted' => 'مقبول', 'in_progress' => 'قيد التنفيذ', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
    $statusColors = ['pending' => 'warning', 'accepted' => 'info', 'in_progress' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary'];
@endphp
<main class="craftman-dashboard py-5">
    <div class="container">
        <section class="craftman-welcome p-4 p-lg-5 mb-4 shadow-sm">
            <span class="text-warning fw-bold">لوحة الحرفي</span>
            <h1 class="h2 fw-bold mt-2">مرحباً، {{ $craftman->user->name }}</h1>
            <p class="text-white-50 mb-0">تابع الطلبات المتاحة وعروضك والأعمال المسندة إليك.</p>
        </section>

        <section class="row g-3 mb-4" aria-label="ملخص النشاط">
            <div class="col-6 col-xl-3"><div class="card dashboard-stat h-100 shadow-sm"><div class="card-body"><small class="text-muted">الطلبات المتاحة</small><div class="display-6 fw-bold text-warning" data-testid="available-orders-count">{{ $summary['availableOrders'] }}</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="card dashboard-stat h-100 shadow-sm"><div class="card-body"><small class="text-muted">عروضي قيد الانتظار</small><div class="display-6 fw-bold text-info" data-testid="pending-offers-count">{{ $summary['pendingOffers'] }}</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="card dashboard-stat h-100 shadow-sm"><div class="card-body"><small class="text-muted">الأعمال النشطة</small><div class="display-6 fw-bold text-primary" data-testid="active-jobs-count">{{ $summary['activeJobs'] }}</div></div></div></div>
            <div class="col-6 col-xl-3"><div class="card dashboard-stat h-100 shadow-sm"><div class="card-body"><small class="text-muted">الأعمال المكتملة</small><div class="display-6 fw-bold text-success" data-testid="completed-jobs-count">{{ $summary['completedJobs'] }}</div></div></div></div>
        </section>

        <section class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3"><a class="card dashboard-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.order.index') }}"><div class="card-body d-flex align-items-center gap-3"><span class="dashboard-icon"><i class="fas fa-list-check"></i></span><strong>تصفح الطلبات المتاحة</strong></div></a></div>
            <div class="col-md-6 col-xl-3"><a class="card dashboard-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.offer.index') }}"><div class="card-body d-flex align-items-center gap-3"><span class="dashboard-icon"><i class="fas fa-file-invoice-dollar"></i></span><strong>عروضي</strong></div></a></div>
            <div class="col-md-6 col-xl-3"><a class="card dashboard-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.craftman.jobs.index') }}"><div class="card-body d-flex align-items-center gap-3"><span class="dashboard-icon"><i class="fas fa-briefcase"></i></span><strong>أعمالي</strong></div></a></div>
            <div class="col-md-6 col-xl-3"><a class="card dashboard-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.craftman.portfolios.index') }}"><div class="card-body d-flex align-items-center gap-3"><span class="dashboard-icon"><i class="fas fa-images"></i></span><strong>معرض أعمالي</strong></div></a></div>
            <div class="col-md-6 col-xl-3"><a class="card dashboard-action h-100 text-decoration-none text-dark shadow-sm" href="{{ route('herafy.craftman.profile.show') }}"><div class="card-body d-flex align-items-center gap-3"><span class="dashboard-icon"><i class="fas fa-id-card"></i></span><strong>عرض الملف الشخصي</strong></div></a></div>
        </section>

        <div class="row g-4">
            <div class="col-lg-6">
                <section class="card activity-card shadow-sm h-100"><div class="card-body p-4"><h2 class="h5 fw-bold mb-3">أحدث العروض</h2>
                    @forelse($recentOffers as $offer)
                        <a href="{{ route('herafy.offer.show', $offer) }}" class="d-flex justify-content-between gap-3 py-3 border-top text-decoration-none text-dark"><span>{{ $offer->order?->title ?? 'طلب غير متاح' }}</span><span class="badge text-bg-{{ $statusColors[$offer->status] ?? 'secondary' }}">{{ $statusLabels[$offer->status] ?? $offer->status }}</span></a>
                    @empty
                        <div class="text-center text-muted py-4"><i class="fas fa-file-circle-plus fa-2x text-warning mb-2"></i><p class="mb-0">لم تقدم أي عروض بعد.</p></div>
                    @endforelse
                </div></section>
            </div>
            <div class="col-lg-6">
                <section class="card activity-card shadow-sm h-100"><div class="card-body p-4"><h2 class="h5 fw-bold mb-3">أحدث الأعمال</h2>
                    @forelse($recentJobs as $job)
                        <a href="{{ route('herafy.craftman.jobs.show', $job) }}" class="d-flex justify-content-between gap-3 py-3 border-top text-decoration-none text-dark"><span>{{ $job->order?->title ?? 'طلب غير متاح' }}</span><span class="badge text-bg-{{ $statusColors[$job->status] ?? 'secondary' }}">{{ $statusLabels[$job->status] ?? $job->status }}</span></a>
                    @empty
                        <div class="text-center text-muted py-4"><i class="fas fa-briefcase fa-2x text-warning mb-2"></i><p class="mb-0">لا توجد أعمال مسندة إليك بعد.</p></div>
                    @endforelse
                </div></section>
            </div>
        </div>
    </div>
</main>
@endsection
