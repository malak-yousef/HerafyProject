@extends('herafy.parent')

@section('title', 'تفاصيل العمل')

@section('content')
<section class="py-5 bg-light min-vh-100">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="fw-bold">{{ $serviceJob->order->title }}</h2>
                        <span class="badge bg-warning text-dark">{{ $serviceJob->status }}</span>
                    </div>
                    <a href="{{ route('herafy.craftman.jobs.index') }}" class="btn btn-outline-secondary">العودة إلى أعمالي</a>
                </div>

                <p class="lead">{{ $serviceJob->order->description }}</p>
                <div class="row g-3">
                    <div class="col-md-6"><strong>العميل:</strong> {{ $serviceJob->order->customer->user->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>المهنة:</strong> {{ $serviceJob->order->profession->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>الموقع:</strong> {{ $serviceJob->order->city->name ?? '-' }} / {{ $serviceJob->order->area->name ?? '-' }}</div>
                    <div class="col-md-6"><strong>السعر المتفق عليه:</strong> {{ $serviceJob->offer->price }}</div>
                    <div class="col-md-6"><strong>تاريخ الخدمة:</strong> {{ $serviceJob->order->service_date?->format('Y-m-d') ?? '-' }}</div>
                    <div class="col-md-6"><strong>الهاتف:</strong> {{ $serviceJob->order->phone ?? '-' }}</div>
                    <div class="col-12"><strong>العنوان:</strong> {{ $serviceJob->order->address ?? '-' }}</div>
                    <div class="col-md-6"><strong>وقت البدء:</strong> {{ $serviceJob->started_at?->format('Y-m-d H:i') ?? '-' }}</div>
                    <div class="col-md-6"><strong>وقت الإكمال:</strong> {{ $serviceJob->completed_at?->format('Y-m-d H:i') ?? '-' }}</div>
                </div>

                @if ($serviceJob->status === 'pending')
                    <hr class="my-4">
                    <form method="POST" action="{{ route('herafy.craftman.jobs.start', $serviceJob) }}"
                          onsubmit="return confirm('هل أنت متأكد من بدء العمل؟');">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success" type="submit">بدء العمل</button>
                    </form>
                @elseif ($serviceJob->status === 'in_progress')
                    <hr class="my-4">
                    <form method="POST" action="{{ route('herafy.craftman.jobs.complete', $serviceJob) }}"
                          onsubmit="return confirm('هل أنت متأكد من إكمال العمل؟');">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-primary" type="submit">إكمال العمل</button>
                    </form>
                @endif

                @if ($serviceJob->review)
                    <hr class="my-4">
                    <h4 class="fw-bold">تقييم العميل</h4>
                    <p class="mb-1"><strong>التقييم:</strong> {{ $serviceJob->review->rating }} / 5</p>
                    <p class="mb-0">{{ $serviceJob->review->comment ?: 'لا يوجد تعليق.' }}</p>
                @endif

                @if ($serviceJob->customerReview)
                    <hr class="my-4">
                    <h4 class="fw-bold">تقييمك للعميل</h4>
                    <p class="mb-1"><strong>التقييم:</strong> {{ $serviceJob->customerReview->rating }} / 5</p>
                    <p class="mb-0">{{ $serviceJob->customerReview->comment ?: 'لا يوجد تعليق.' }}</p>
                @elseif ($serviceJob->status === 'completed')
                    <hr class="my-4">
                    <h4 class="fw-bold">قيّم العميل</h4>
                    <p class="text-muted">قيّم الالتزام والتواصل بعد انتهاء العمل.</p>
                    <form method="POST" action="{{ route('herafy.craftman.jobs.customer-review.store', $serviceJob) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="customer-rating" class="form-label">التقييم</label>
                            <select id="customer-rating" name="rating" class="form-select" required>
                                <option value="">اختر من 1 إلى 5</option>
                                @for($rating = 1; $rating <= 5; $rating++)
                                    <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }}</option>
                                @endfor
                            </select>
                            @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer-comment" class="form-label">التعليق (اختياري)</label>
                            <textarea id="customer-comment" name="comment" class="form-control" rows="4" maxlength="2000">{{ old('comment') }}</textarea>
                            @error('comment')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-warning">إرسال تقييم العميل</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
