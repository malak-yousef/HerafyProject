@extends('herafy.parent')

@section('title', 'الإشعارات')

@section('content')
<main class="bg-light py-5" style="min-height: 65vh">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold mb-0"><i class="fas fa-bell text-warning ms-2"></i>الإشعارات</h1>
            @if((auth('customer')->user() ?? auth('craftman')->user())->unreadNotifications()->exists())
                <form method="POST" action="{{ route($readAllRoute) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-secondary btn-sm">تحديد الكل كمقروء</button>
                </form>
            @endif
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 20px">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <article class="list-group-item p-4 {{ $notification->read_at ? '' : 'bg-warning-subtle' }}">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <h2 class="h6 fw-bold mb-1">{{ $notification->data['title'] ?? 'إشعار' }}</h2>
                                <p class="text-muted mb-2">{{ $notification->data['message'] ?? '' }}</p>
                                @if(! empty($notification->data['order_title']))
                                    <p class="small mb-2">{{ $notification->data['order_title'] }}</p>
                                @endif
                                @if(! empty($notification->data['comment_preview']))
                                    <p class="small text-muted mb-2">{{ $notification->data['comment_preview'] }}</p>
                                @endif
                                <small class="text-muted">{{ $notification->created_at?->diffForHumans() }}</small>
                            </div>
                            @if(in_array(($notification->data['event'] ?? null), ['new_offer_submitted', 'offer_accepted', 'review_submitted', 'verification_status_changed'], true))
                                <form method="POST" action="{{ route($readRoute, $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-warning">عرض الطلب</button>
                                </form>
                            @elseif(! $notification->read_at)
                                <form method="POST" action="{{ route($readRoute, $notification->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">تحديد كمقروء</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="text-center text-muted p-5"><i class="fas fa-bell-slash fa-2x mb-3"></i><p class="mb-0">لا توجد إشعارات بعد.</p></div>
                @endforelse
            </div>
        </div>
        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</main>
@endsection
