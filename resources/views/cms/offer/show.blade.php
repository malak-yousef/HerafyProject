@extends('cms.parent')

@section('title','تفاصيل العرض')

@section('styles')

@endsection

@section('content')

<div class="details-card">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="page-title">

            <i class="fa-solid fa-hand-holding-dollar"></i>

            تفاصيل العرض

        </h3>

        <a href="{{ route('offers.index') }}"
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
                    <strong>صاحب الطلب:</strong>
                    {{ $offer->order->customer->user->name ?? '-' }}
                </p>

            </div>

        </div>

        {{-- معلومات الحرفي --}}
        <div class="col-md-6">

            <div class="info-box">

                <h5 class="info-title">

                    <i class="fa-solid fa-user-gear"></i>

                    معلومات الحرفي

                </h5>

                <p class="info-value">
                    <strong>الاسم:</strong>
                    {{ $offer->craftman->user->name ?? '-' }}
                </p>

                <p class="info-value">
                    <strong>الهاتف:</strong>
                    {{ $offer->craftman->user->mobile ?? '-' }}
                </p>

            </div>

        </div>

        {{-- تفاصيل العرض --}}
        <div class="col-12">

            <div class="info-box">

                <h5 class="info-title">

                    <i class="fa-solid fa-file-lines"></i>

                    تفاصيل العرض

                </h5>

                <p class="info-value">
                    <strong>السعر:</strong>
                    {{ $offer->price }} ₪
                </p>

                <p class="info-value">
                    <strong>الرسالة:</strong>
                    {{ $offer->message }}
                </p>

            </div>

        </div>

        {{-- الحالة --}}
        <div class="col-md-6">

            <div class="info-box">

                <h5 class="info-title">

                    الحالة

                </h5>

                <span class="status {{ $offer->status }}">

                    @if($offer->status=='pending')

                        قيد الانتظار

                    @elseif($offer->status=='accepted')

                        مقبول

                    @elseif($offer->status=='in_progress')

                        قيد التنفيذ

                    @elseif($offer->status=='completed')

                        مكتمل

                    @else

                        ملغي

                    @endif

                </span>

            </div>

        </div>

        {{-- تاريخ الإنشاء --}}
        <div class="col-md-6">

            <div class="info-box">

                <h5 class="info-title">

                    التاريخ

                </h5>

                <p class="info-value">
                    {{ $offer->created_at->format('Y-m-d') }}
                </p>

            </div>

        </div>

    </div>

</div>

@endsection