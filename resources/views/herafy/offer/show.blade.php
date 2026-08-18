@extends('herafy.parent')

@section('title', 'تفاصيل العرض')

@section('style')

<style>
    .offer-section {
        min-height: 80vh;
        background: #f8f9fa;
    }

    .details-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .06);
    }

    .page-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        height: 100%;
    }

    .info-title {
        color: #0b1f3a;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .info-value {
        color: #555;
        margin-bottom: 10px;
    }

    .price {
        color: #d4af37;
        font-size: 24px;
        font-weight: 700;
    }

    .status {
        display: inline-block;
        padding: 7px 18px;
        border-radius: 25px;
        font-weight: bold;
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
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
    }

    .btn-gold:hover {
        background: #b89625;
        color: #fff;
    }
</style>

@endsection


@section('content')

<section class="offer-section py-5">

    <div class="container">

        <div class="details-card">

            {{-- العنوان --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <h2 class="page-title">

                    <i class="fa-solid fa-hand-holding-dollar"></i>

                    تفاصيل العرض

                </h2>

                <a href="{{ route('herafy.offer.index') }}"
                   class="btn btn-secondary">

                    رجوع

                </a>

            </div>


            <div class="row g-4">

                {{-- معلومات الطلب --}}
                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="info-title">

                            <i class="fa-solid fa-clipboard-list"></i>

                            معلومات الطلب

                        </h5>

                        <p class="info-value">

                            <strong>عنوان الطلب:</strong>

                            {{ $offer->order->title ?? '-' }}

                        </p>

                        <p class="info-value">

                            <strong>الوصف:</strong>

                            {{ $offer->order->description ?? '-' }}

                        </p>

                        <p class="info-value">

                            <strong>الميزانية:</strong>

                            {{ $offer->order->budget ?? 'غير محددة' }} ₪

                        </p>

                    </div>

                </div>


                {{-- تفاصيل العرض --}}
                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="info-title">

                            <i class="fa-solid fa-file-invoice-dollar"></i>

                            تفاصيل العرض

                        </h5>

                        <p class="info-value">

                            <strong>السعر المقترح:</strong>

                        </p>

                        <div class="price mb-3">

                            {{ $offer->price }} ₪

                        </div>

                        <p class="info-value">

                            <strong>رسالة العرض:</strong>

                            {{ $offer->message }}

                        </p>

                    </div>

                </div>


                {{-- حالة العرض --}}
                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="info-title">

                            الحالة

                        </h5>

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

                </div>


                {{-- تاريخ العرض --}}
                <div class="col-md-6">

                    <div class="info-box">

                        <h5 class="info-title">

                            التاريخ

                        </h5>

                        <p class="info-value">

                            <strong>تاريخ تقديم العرض:</strong>

                            {{ $offer->created_at?->format('Y-m-d') ?? '-' }}

                        </p>

                    </div>

                </div>

            </div>


            {{-- الخدمة المرتبطة --}}
            @if($offer->serviceJob)

                <div class="info-box mt-4">

                    <h5 class="info-title">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                        الخدمة المرتبطة بالعرض

                    </h5>

                    <p class="info-value">

                        <strong>حالة الخدمة:</strong>

                        @if($offer->serviceJob->status === 'pending')
                            قيد الانتظار

                        @elseif($offer->serviceJob->status === 'in_progress')
                            قيد التنفيذ

                        @elseif($offer->serviceJob->status === 'completed')
                            مكتملة

                        @elseif($offer->serviceJob->status === 'cancelled')
                            ملغاة

                        @endif

                    </p>

                    <a href="{{ route('service-jobs.show', $offer->serviceJob) }}"
                       class="btn btn-info btn-sm">

                        <i class="fa-solid fa-eye"></i>

                        عرض الخدمة

                    </a>

                </div>

            @endif

        </div>

    </div>

</section>

@endsection