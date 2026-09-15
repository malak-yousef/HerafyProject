@extends('herafy.parent')

@section('title', 'تفاصيل الطلب')

@section('content')
<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h2 class="fw-bold">{{ $order->title }}</h2>
                        <span class="badge {{ $order->status === 'pending' ? 'bg-warning text-dark' : ($order->status === 'cancelled' ? 'bg-danger' : 'bg-secondary') }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <a href="{{ route('herafy.customer.orders.index') }}" class="btn btn-outline-secondary">العودة إلى طلباتي</a>
                </div>

                <p class="lead">{{ $order->description }}</p>

                <div class="row g-3 mt-2">
                    <div class="col-md-6"><strong>المهنة:</strong> {{ $order->profession->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>الموقع:</strong> {{ $order->city->name ?? '-' }} / {{ $order->area->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>الميزانية:</strong> {{ $order->budget ?? '-' }}</div>
                    <div class="col-md-6"><strong>تاريخ الخدمة:</strong> {{ $order->service_date?->format('Y-m-d') ?? '-' }}</div>
                    <div class="col-md-6"><strong>العنوان:</strong> {{ $order->address ?? '-' }}</div>
                    <div class="col-md-6"><strong>الهاتف:</strong> {{ $order->phone ?? '-' }}</div>
                </div>

                @if ($order->images->isNotEmpty())
                    <hr class="my-4">
                    <h4 class="fw-bold mb-3">صور الطلب</h4>
                    <div class="row g-3">
                        @foreach ($order->images as $image)
                            <div class="col-6 col-md-3">
                                <a href="{{ $image->publicUrl() }}" target="_blank" rel="noopener">
                                    <img src="{{ $image->publicUrl() }}" alt="صورة الطلب" class="img-fluid rounded-3 border" style="height: 160px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($order->status === 'pending')
                    <hr class="my-4">
                    <form method="POST" action="{{ route('herafy.customer.orders.cancel', $order) }}"
                          onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger">إلغاء الطلب</button>
                    </form>
                @endif
            </div>
        </div>

        @if ($order->serviceJob)
            <div class="card border-success shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold text-success mb-3"><i class="fas fa-briefcase"></i> اتفاق العمل</h3>
                    <div class="row g-3">
                        <div class="col-md-4"><strong>الحرفي:</strong> {{ $order->serviceJob->craftman->user->name ?? '-' }}</div>
                        <div class="col-md-4"><strong>السعر المتفق عليه:</strong> {{ $order->serviceJob->offer->price ?? '-' }}</div>
                        <div class="col-md-4"><strong>حالة العمل:</strong> {{ $order->serviceJob->status }}</div>
                        <div class="col-md-4"><strong>وقت البدء:</strong> {{ $order->serviceJob->started_at?->format('Y-m-d H:i') ?? '-' }}</div>
                        <div class="col-md-4"><strong>وقت الإكمال:</strong> {{ $order->serviceJob->completed_at?->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>

                    @if ($order->serviceJob->review)
                        <hr class="my-4">
                        <h5 class="fw-bold">تقييمك</h5>
                        <p class="mb-1"><strong>النجوم:</strong> {{ $order->serviceJob->review->rating }} / 5</p>
                        <p class="mb-0">{{ $order->serviceJob->review->comment ?: 'لا يوجد تعليق.' }}</p>
                    @elseif ($order->serviceJob->status === 'completed')
                        <hr class="my-4">
                        <h5 class="fw-bold">قيّم العمل</h5>
                        <form method="POST" action="{{ route('herafy.customer.jobs.review.store', $order->serviceJob) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label">التقييم</label>
                                <select id="rating" name="rating" class="form-select" required>
                                    <option value="">اختر من 1 إلى 5</option>
                                    @for ($rating = 1; $rating <= 5; $rating++)
                                        <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">التعليق (اختياري)</label>
                                <textarea id="comment" name="comment" class="form-control" rows="4" maxlength="2000">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-warning">إرسال التقييم</button>
                        </form>
                    @endif

                    @if ($order->serviceJob->customerReview)
                        <hr class="my-4">
                        <h5 class="fw-bold">تقييم الحرفي لك</h5>
                        <p class="mb-1"><strong>النجوم:</strong> {{ $order->serviceJob->customerReview->rating }} / 5</p>
                        <p class="mb-0">{{ $order->serviceJob->customerReview->comment ?: 'لا يوجد تعليق.' }}</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <h3 class="fw-bold mb-4">العروض المقدمة</h3>
                @forelse ($order->offers as $offer)
                    <div class="border rounded-3 p-3 mb-3 {{ $offer->status === 'accepted' ? 'border-success bg-success-subtle' : '' }}">
                        <div class="d-flex justify-content-between gap-3">
                            <strong>{{ $offer->craftman->user->name ?? 'حرفي' }}</strong>
                            <span class="fw-bold text-warning">{{ $offer->price }}</span>
                        </div>
                        <p class="text-muted mb-1 mt-2">{{ $offer->message }}</p>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-2">
                            <small class="text-muted">
                                الحالة:
                                @if ($offer->status === 'accepted')
                                    <span class="badge bg-success">مقبول</span>
                                @elseif ($order->status !== 'pending' && $offer->status === 'pending')
                                    <span class="badge bg-secondary">غير مختار</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $offer->status }}</span>
                                @endif
                            </small>

                            @if ($order->status === 'pending' && $offer->status === 'pending')
                                <form method="POST" action="{{ route('herafy.customer.orders.offers.accept', [$order, $offer]) }}"
                                      onsubmit="return confirm('هل أنت متأكد من قبول هذا العرض؟');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">قبول العرض</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info mb-0">لا توجد عروض مقدمة على هذا الطلب بعد.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
