@extends('cms.parent')

@section('title', 'لوحة التحكم')
@section('main_content', 'الرئيسية')

@section('styles')
<style>
    .dashboard-page {
        --dashboard-purple: #6c5ce7;
        --dashboard-green: #00b894;
        --dashboard-blue: #0984e3;
        --dashboard-orange: #e17055;
        --dashboard-gold: #f1c40f;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        min-height: 116px;
        background: #fff;
        border: 1px solid #f1f2f6;
        border-right: 4px solid var(--card-color);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .01);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        color: inherit;
        text-decoration: none;
        transition: all .25s ease;
    }

    .stat-card:hover {
        color: inherit;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, .04);
    }

    .stat-number {
        color: #2c3e50;
        font-size: 26px;
        font-weight: 700;
    }

    .stat-label {
        color: #8a94a6;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: color-mix(in srgb, var(--card-color) 15%, transparent);
        color: var(--card-color);
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .dashboard-panel {
        min-width: 0;
        background: #fff;
        border: 1px solid #f1f2f6;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .01);
        overflow: hidden;
    }

    .dashboard-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f2f6;
    }

    .dashboard-panel-header h5 {
        margin: 0;
        color: #2c3e50;
        font-size: 16px;
        font-weight: 700;
    }

    .status-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(105px, 1fr));
        gap: 12px;
        padding: 20px;
    }

    .status-item {
        padding: 14px 10px;
        border-radius: 12px;
        background: #f8f9fb;
        text-align: center;
    }

    .status-item strong {
        display: block;
        color: #2c3e50;
        font-size: 20px;
    }

    .status-item span {
        color: #8a94a6;
        font-size: 12px;
    }

    .dashboard-table-wrap {
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;
        min-width: 580px;
        margin: 0;
    }

    .dashboard-table th,
    .dashboard-table td {
        padding: 13px 16px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .dashboard-table th {
        color: #6b7280;
        background: #fafbfc;
        font-size: 12px;
        font-weight: 600;
    }

    .dashboard-table td {
        color: #374151;
        border-top: 1px solid #f1f2f6;
        font-size: 13px;
    }

    .dashboard-empty {
        padding: 32px 20px;
        color: #8a94a6;
        text-align: center;
    }

    .dashboard-empty i {
        display: block;
        margin-bottom: 10px;
        color: #cbd5e1;
        font-size: 26px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-pending { background: #fff7d6; color: #946c00; }
    .status-accepted, .status-approved { background: #dcfce7; color: #16794a; }
    .status-in_progress { background: #dbeafe; color: #1d4ed8; }
    .status-completed { background: #d1fae5; color: #047857; }
    .status-cancelled, .status-rejected { background: #fee2e2; color: #b91c1c; }

    @media (max-width: 991.98px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 575.98px) {
        .stats-grid { grid-template-columns: 1fr; gap: 14px; }
        .stat-card { min-height: 100px; padding: 18px; }
        .dashboard-panel-header { align-items: flex-start; flex-direction: column; }
    }
</style>
@endsection

@section('content')
@php
    $statusLabels = [
        'pending' => 'معلق',
        'accepted' => 'مقبول',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'approved' => 'معتمد',
        'rejected' => 'مرفوض',
    ];
@endphp

<div class="dashboard-page" data-testid="admin-dashboard">
    <div class="stats-grid">
        @can('customers.view')<a href="{{ route('customers.index') }}" class="stat-card" style="--card-color: #6c5ce7;">@else<div class="stat-card" style="--card-color: #6c5ce7;">@endcan
            <div><div class="stat-number" data-testid="active-customers-count">{{ $metrics['active_customers'] }}</div><div class="stat-label">الزبائن النشطون</div></div>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        @can('customers.view')</a>@else</div>@endcan

        @can('craftmen.view')<a href="{{ route('craftmen.index') }}" class="stat-card" style="--card-color: #00b894;">@else<div class="stat-card" style="--card-color: #00b894;">@endcan
            <div><div class="stat-number" data-testid="active-craftmen-count">{{ $metrics['active_craftmen'] }}</div><div class="stat-label">الحرفيون النشطون</div></div>
            <div class="stat-icon"><i class="fas fa-tools"></i></div>
        @can('craftmen.view')</a>@else</div>@endcan

        @can('orders.view')<a href="{{ route('orders.index') }}" class="stat-card" style="--card-color: #0984e3;">@else<div class="stat-card" style="--card-color: #0984e3;">@endcan
            <div><div class="stat-number" data-testid="pending-orders-count">{{ $metrics['pending_orders'] }}</div><div class="stat-label">الطلبات المعلقة</div></div>
            <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
        @can('orders.view')</a>@else</div>@endcan

        @can('offers.view')<a href="{{ route('offers.index') }}" class="stat-card" style="--card-color: #e17055;">@else<div class="stat-card" style="--card-color: #e17055;">@endcan
            <div><div class="stat-number" data-testid="pending-offers-count">{{ $metrics['pending_offers'] }}</div><div class="stat-label">العروض المعلقة</div></div>
            <div class="stat-icon"><i class="fas fa-handshake"></i></div>
        @can('offers.view')</a>@else</div>@endcan

        @can('service-jobs.view')<a href="{{ route('service-jobs.index') }}" class="stat-card" style="--card-color: #f1c40f;">@else<div class="stat-card" style="--card-color: #f1c40f;">@endcan
            <div><div class="stat-number" data-testid="active-service-jobs-count">{{ $metrics['active_service_jobs'] }}</div><div class="stat-label">الأعمال النشطة</div></div>
            <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
        @can('service-jobs.view')</a>@else</div>@endcan

        @can('reviews.view')<a href="{{ route('reviews.index') }}" class="stat-card" style="--card-color: #fd79a8;">@else<div class="stat-card" style="--card-color: #fd79a8;">@endcan
            <div><div class="stat-number" data-testid="pending-reviews-count">{{ $metrics['pending_reviews'] }}</div><div class="stat-label">التقييمات المعلقة</div></div>
            <div class="stat-icon"><i class="fas fa-star"></i></div>
        @can('reviews.view')</a>@else</div>@endcan

        @can('verifications.view')<a href="{{ route('verifications.index') }}" class="stat-card" style="--card-color: #00cec9;">@else<div class="stat-card" style="--card-color: #00cec9;">@endcan
            <div><div class="stat-number" data-testid="pending-verifications-count">{{ $metrics['pending_verifications'] }}</div><div class="stat-label">طلبات التوثيق المعلقة</div></div>
            <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        @can('verifications.view')</a>@else</div>@endcan

        @can('portfolios.view')<a href="{{ route('portfolios.index') }}" class="stat-card" style="--card-color: #8e44ad;">@else<div class="stat-card" style="--card-color: #8e44ad;">@endcan
            <div><div class="stat-number" data-testid="active-portfolios-count">{{ $metrics['active_portfolios'] }}</div><div class="stat-label">معارض الأعمال النشطة</div></div>
            <div class="stat-icon"><i class="fas fa-images"></i></div>
        @can('portfolios.view')</a>@else</div>@endcan
    </div>

    <div class="dashboard-grid">
        @foreach ([
            ['title' => 'حالات الطلبات', 'icon' => 'clipboard-list', 'counts' => $statusSummaries['orders']],
            ['title' => 'حالات الأعمال', 'icon' => 'briefcase', 'counts' => $statusSummaries['service_jobs']],
            ['title' => 'حالات التقييمات', 'icon' => 'star', 'counts' => $statusSummaries['reviews']],
            ['title' => 'حالات التوثيق', 'icon' => 'user-check', 'counts' => $statusSummaries['verifications']],
        ] as $summary)
            <section class="dashboard-panel">
                <div class="dashboard-panel-header"><h5><i class="fas fa-{{ $summary['icon'] }} text-warning"></i> {{ $summary['title'] }}</h5></div>
                <div class="status-summary">
                    @foreach ($summary['counts'] as $status => $count)
                        <div class="status-item" data-testid="status-{{ Str::slug($summary['title']) }}-{{ $status }}">
                            <strong>{{ $count }}</strong><span>{{ $statusLabels[$status] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="dashboard-grid">
        @can('verifications.view')
            <section class="dashboard-panel" data-testid="pending-verifications-list">
                <div class="dashboard-panel-header"><h5><i class="fas fa-user-check text-warning"></i> طلبات التوثيق المعلقة</h5><a href="{{ route('verifications.index') }}" class="btn btn-sm btn-outline-warning">عرض الكل</a></div>
                @if ($pendingVerifications->isEmpty())
                    <div class="dashboard-empty"><i class="fas fa-inbox"></i>لا توجد طلبات توثيق معلقة</div>
                @else
                    <div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>الحرفي</th><th>تاريخ التقديم</th><th>الحالة</th><th></th></tr></thead><tbody>
                    @foreach ($pendingVerifications as $verification)
                        <tr><td>{{ $verification->craftman?->user?->name ?? 'غير متاح' }}</td><td>{{ $verification->created_at?->format('Y-m-d') }}</td><td><span class="status-badge status-pending">معلق</span></td><td><a href="{{ route('verifications.show', $verification) }}" class="btn btn-sm btn-outline-primary">إدارة</a></td></tr>
                    @endforeach
                    </tbody></table></div>
                @endif
            </section>
        @endcan

        @can('reviews.view')
            <section class="dashboard-panel" data-testid="pending-reviews-list">
                <div class="dashboard-panel-header"><h5><i class="fas fa-star text-warning"></i> التقييمات المعلقة</h5><a href="{{ route('reviews.index') }}" class="btn btn-sm btn-outline-warning">عرض الكل</a></div>
                @if ($pendingReviews->isEmpty())
                    <div class="dashboard-empty"><i class="fas fa-inbox"></i>لا توجد مراجعات معلقة</div>
                @else
                    <div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>الحرفي</th><th>التقييم</th><th>الطلب</th><th>تاريخ التقديم</th><th></th></tr></thead><tbody>
                    @foreach ($pendingReviews as $review)
                        <tr><td>{{ $review->craftsman?->user?->name ?? 'غير متاح' }}</td><td>{{ $review->rating }} / 5</td><td>{{ $review->request?->title ?? 'غير متاح' }}</td><td>{{ $review->created_at?->format('Y-m-d') }}</td><td><a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">إدارة</a></td></tr>
                    @endforeach
                    </tbody></table></div>
                @endif
            </section>
        @endcan
    </div>

    <div class="dashboard-grid">
        <section class="dashboard-panel" data-testid="recent-orders-list">
            <div class="dashboard-panel-header"><h5><i class="fas fa-bolt text-warning"></i> أحدث الطلبات</h5>@can('orders.view')<a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-warning">عرض الكل</a>@endcan</div>
            @if ($recentOrders->isEmpty())
                <div class="dashboard-empty"><i class="fas fa-inbox"></i>لا توجد طلبات حديثة</div>
            @else
                <div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>الطلب</th><th>الزبون</th><th>المهنة</th><th>الحالة</th><th>التاريخ</th></tr></thead><tbody>
                @foreach ($recentOrders as $order)
                    <tr><td>{{ $order->title }}</td><td>{{ $order->customer?->user?->name ?? 'غير متاح' }}</td><td>{{ $order->profession?->name ?? 'غير متاح' }}</td><td><span class="status-badge status-{{ $order->status }}">{{ $statusLabels[$order->status] ?? $order->status }}</span></td><td>{{ $order->created_at?->format('Y-m-d') }}</td></tr>
                @endforeach
                </tbody></table></div>
            @endif
        </section>

        <section class="dashboard-panel" data-testid="recent-service-jobs-list">
            <div class="dashboard-panel-header"><h5><i class="fas fa-briefcase text-warning"></i> أحدث الأعمال</h5>@can('service-jobs.view')<a href="{{ route('service-jobs.index') }}" class="btn btn-sm btn-outline-warning">عرض الكل</a>@endcan</div>
            @if ($recentServiceJobs->isEmpty())
                <div class="dashboard-empty"><i class="fas fa-inbox"></i>لا توجد أعمال حديثة</div>
            @else
                <div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>الطلب</th><th>الزبون</th><th>الحرفي</th><th>الحالة</th><th>آخر تحديث</th></tr></thead><tbody>
                @foreach ($recentServiceJobs as $job)
                    <tr><td>{{ $job->order?->title ?? 'غير متاح' }}</td><td>{{ $job->customer?->user?->name ?? 'غير متاح' }}</td><td>{{ $job->craftman?->user?->name ?? 'غير متاح' }}</td><td><span class="status-badge status-{{ $job->status }}">{{ $statusLabels[$job->status] ?? $job->status }}</span></td><td>{{ $job->updated_at?->format('Y-m-d') }}</td></tr>
                @endforeach
                </tbody></table></div>
            @endif
        </section>
    </div>
</div>
@endsection
