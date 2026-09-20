@extends('herafy.parent')

@section('title', 'تفاصيل الطلب')

@section('style')
<style>
    .order-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        height: 100%;
    }

    .section-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .offer-card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .06);
        margin-bottom: 15px;
    }

    .btn-gold {
        background: #d4af37;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
    }

    .btn-gold:hover {
        background: #b89625;
        color: #fff;
    }

    .price {
        color: #b89625;
        font-size: 20px;
        font-weight: 700;
    }
</style>
@endsection


@section('content')

<div class="container py-5">


    <div class="card shadow-lg order-card">

        <div class="card-body p-4 p-md-5">

            {{-- العنوان والأزرار --}}

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h2 class="fw-bold mb-0">
                    <i class="fas fa-file-lines text-warning"></i>
                    تفاصيل الطلب
                </h2>

                <div class="d-flex gap-2">

                    <a href="{{ route('herafy.order.index') }}"
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i>
                        رجوع
                    </a>

                </div>

            </div>


            {{-- عنوان الطلب --}}

            <div class="info-box mb-4">

                <h4 class="fw-bold">
                    {{ $order->title }}
                </h4>

                <p class="text-muted mb-0">
                    {{ $order->description }}
                </p>

            </div>


            {{-- معلومات الطلب --}}

            <div class="row g-4">

                {{-- الموقع --}}

                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="section-title mb-3">

                            <i class="fas fa-location-dot text-warning"></i>
                            الموقع

                        </h5>

                        <p>
                            <strong>المدينة:</strong>
                            {{ $order->city->name ?? '-' }}
                        </p>

                        <p>
                            <strong>المنطقة:</strong>
                            {{ $order->area->name ?? '-' }}
                        </p>

                        <p class="mb-0">
                            <strong>العنوان:</strong>
                            {{ $order->address ?? '-' }}
                        </p>

                    </div>

                </div>


                {{-- معلومات إضافية --}}

                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="section-title mb-3">

                            <i class="fas fa-circle-info text-warning"></i>
                            معلومات الطلب

                        </h5>

                        <p>

                            <strong>الميزانية:</strong>

                            {{ $order->budget ?? 'غير محددة' }} ₪

                        </p>

                        <p>

                            <strong>التاريخ المطلوب:</strong>

                            {{ $order->service_date ?? '-' }}

                        </p>

                        <p class="mb-0">

                            <strong>الحالة:</strong>

                            @if($order->status === 'pending')

                                <span class="badge bg-warning text-dark">
                                    قيد الانتظار
                                </span>

                            @elseif($order->status === 'accepted')

                                <span class="badge bg-success">
                                    مقبول
                                </span>

                            @elseif($order->status === 'completed')

                                <span class="badge bg-primary">
                                    مكتمل
                                </span>

                            @elseif($order->status === 'cancelled')

                                <span class="badge bg-danger">
                                    ملغي
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ $order->status }}
                                </span>

                            @endif

                        </p>

                    </div>

                </div>

            </div>


            @if($order->images->isNotEmpty())
                <section class="mt-5" aria-labelledby="order-images-title">
                    <h4 id="order-images-title" class="section-title mb-4">
                        <i class="fas fa-images text-warning"></i> صور الطلب
                    </h4>
                    <div class="row g-3">
                        @foreach($order->images as $image)
                            <div class="col-6 col-md-4 col-lg-3">
                                <a href="{{ $image->publicUrl() }}" target="_blank" rel="noopener" class="d-block">
                                    <img src="{{ $image->publicUrl() }}" alt="صورة مرفقة بطلب {{ $order->title }}" class="img-fluid rounded-3 border shadow-sm" style="height: 180px; width: 100%; object-fit: cover;" width="180" height="180" loading="lazy" decoding="async">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ============================= --}}
            {{-- العروض المقدمة --}}
            {{-- ============================= --}}

            <div class="mt-5">

                <h4 class="section-title mb-4">

                    <i class="fas fa-hand-holding-dollar text-warning"></i>

                    العروض المقدمة

                </h4>


                @if($order->offers->count() > 0)

                    @foreach($order->offers as $offer)

                        <div class="card offer-card">

                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <h5 class="fw-bold mb-0">

                                        <i class="fas fa-user text-warning"></i>

                                        {{ $offer->craftman->user->name ?? 'حرفي' }}

                                    </h5>


                                    @if($offer->status === 'pending')

                                        <span class="badge bg-warning text-dark">
                                            قيد الانتظار
                                        </span>

                                    @elseif($offer->status === 'accepted')

                                        <span class="badge bg-success">
                                            مقبول
                                        </span>

                                    @elseif($offer->status === 'rejected')

                                        <span class="badge bg-danger">
                                            مرفوض
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ $offer->status }}
                                        </span>

                                    @endif

                                </div>


                                <div class="mb-3">

                                    <strong>

                                        <i class="fas fa-money-bill text-warning"></i>

                                        السعر:

                                    </strong>

                                    <span class="price">

                                        {{ $offer->price }} ₪

                                    </span>

                                </div>


                                <div>

                                    <strong>

                                        <i class="fas fa-message text-warning"></i>

                                        رسالة العرض:

                                    </strong>

                                    <p class="text-muted mt-2 mb-0">

                                        {{ $offer->message }}

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @else

                    <div class="alert alert-info">

                        <i class="fas fa-info-circle"></i>

                        لا توجد عروض مقدمة على هذا الطلب حتى الآن.

                    </div>

                @endif

            </div>


            {{-- ============================= --}}
            {{-- زر تقديم العرض --}}
            {{-- ============================= --}}

            @if($order->status === 'pending')

                @php

                    $currentCraftman = auth('craftman')->user()->actor;

                    $hasOffered = $order->offers
                        ->where('craftman_id', $currentCraftman->id)
                        ->count() > 0;

                @endphp


                <div class="text-center mt-5">

                    @if($hasOffered)

                        <div class="alert alert-success">

                            <i class="fas fa-check-circle"></i>

                            لقد قدمت عرضًا على هذا الطلب مسبقًا.

                        </div>

                    @else

                        <a href="{{ route('herafy.offer.create', ['order' => $order->id]) }}"
                           class="btn btn-gold">

                            <i class="fas fa-hand-holding-dollar"></i>

                            تقديم عرض

                        </a>

                    @endif

                </div>

            @endif


        </div>

    </div>

</div>

@endsection
