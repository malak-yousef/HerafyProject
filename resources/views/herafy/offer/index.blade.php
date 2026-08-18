@extends('herafy.parent')

@section('title', 'عروضي')

@section('style')
<style>
    .offers-section {
        min-height: 80vh;
        background: #f8f9fa;
    }

    .page-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .offer-card {
        background: #fff;
        border: 0;
        border-radius: 18px;
        padding: 22px;
        height: 100%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .06);
        transition: .3s;
    }

    .offer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, .1);
    }

    .offer-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .offer-info {
        color: #666;
        margin-bottom: 8px;
    }

    .price {
        color: #d4af37;
        font-size: 20px;
        font-weight: 700;
    }

    .status {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }

    .pending {
        background: #fff3cd;
        color: #856404;
    }

    .accepted {
        background: #d1e7dd;
        color: #0f5132;
    }

    .in_progress {
        background: #cfe2ff;
        color: #084298;
    }

    .completed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .cancelled {
        background: #f8d7da;
        color: #842029;
    }

    .btn-gold {
        background: #d4af37;
        color: #fff;
        border-radius: 10px;
        padding: 8px 18px;
        border: none;
    }

    .btn-gold:hover {
        background: #b89625;
        color: #fff;
    }

    .empty-box {
        background: #fff;
        border-radius: 18px;
        padding: 50px 20px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,.05);
    }
</style>
@endsection

@section('content')

<section class="offers-section py-5">

    <div class="container">

        {{-- العنوان --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="page-title">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    عروضي
                </h2>

                <p class="text-muted mb-0">
                    جميع العروض التي قمت بتقديمها
                </p>
            </div>

        </div>


        {{-- لا توجد عروض --}}
        @if($offers->count() == 0)

            <div class="empty-box">

                <i class="fa-solid fa-file-circle-xmark fa-3x text-muted mb-3"></i>

                <h4>
                    لا توجد عروض حتى الآن
                </h4>

                <p class="text-muted">
                    يمكنك تصفح الطلبات المتاحة وتقديم عروض عليها.
                </p>

                <a href="{{ route('herafy.order.index') }}"
                   class="btn btn-gold">

                    <i class="fa-solid fa-list"></i>
                    تصفح الطلبات

                </a>

            </div>

        @else

            {{-- العروض --}}
            <div class="row g-4">

                @foreach($offers as $offer)

                    <div class="col-lg-6 col-xl-4">

                        <div class="offer-card">

                            {{-- عنوان الطلب --}}
                            <h5 class="offer-title mb-3">

                                <i class="fa-solid fa-clipboard-list"></i>

                                {{ $offer->order->title ?? 'طلب بدون عنوان' }}

                            </h5>


                            {{-- السعر --}}
                            <div class="mb-3">

                                <span class="offer-info">
                                    السعر المقترح:
                                </span>

                                <div class="price">
                                    {{ $offer->price }} ₪
                                </div>

                            </div>


                            {{-- الرسالة --}}
                            <div class="mb-3">

                                <strong>
                                    الرسالة:
                                </strong>

                                <p class="text-muted mt-1">

                                    {{ Str::limit($offer->message, 100) }}

                                </p>

                            </div>


                            {{-- الحالة --}}
                            <div class="mb-3">

                                <strong>
                                    الحالة:
                                </strong>

                                <br>

                                <span class="status {{ $offer->status }}">

                                    @if($offer->status == 'pending')
                                        قيد الانتظار

                                    @elseif($offer->status == 'accepted')
                                        مقبول

                                    @elseif($offer->status == 'in_progress')
                                        قيد التنفيذ

                                    @elseif($offer->status == 'completed')
                                        مكتمل

                                    @elseif($offer->status == 'cancelled')
                                        ملغي

                                    @endif

                                </span>

                            </div>


                            {{-- التاريخ --}}
                            <div class="offer-info mb-3">

                                <i class="fa-regular fa-calendar"></i>

                                تاريخ التقديم:

                                {{ $offer->created_at->format('Y-m-d') }}

                            </div>


                            {{-- التفاصيل --}}
                            <a href="{{ route('herafy.offer.show', $offer) }}"
                               class="btn btn-gold w-100">

                                <i class="fa-solid fa-eye"></i>

                                عرض التفاصيل

                            </a>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- Pagination --}}
            <div class="mt-4">

                {{ $offers->links() }}

            </div>

        @endif

    </div>

</section>

@endsection