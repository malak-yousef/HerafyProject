@extends('herafy.parent')

@section('title', 'تقديم عرض')

@section('style')
<style>
    .offer-section {
        min-height: 80vh;
        background: #f8f9fa;
    }

    .form-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .06);
    }

    .page-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .order-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .order-title {
        color: #0b1f3a;
        font-weight: 700;
    }

    .order-info {
        color: #555;
        margin-bottom: 8px;
    }

    .form-label {
        font-weight: 600;
        color: #0b1f3a;
    }

    .form-control {
        border-radius: 10px;
        padding: 12px;
    }

    .form-control:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 .2rem rgba(212, 175, 55, .2);
    }

    .btn-gold {
        background: #d4af37;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 11px 25px;
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

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="form-card">

                    {{-- العنوان --}}
                    <div class="text-center mb-4">

                        <h2 class="page-title">

                            <i class="fa-solid fa-hand-holding-dollar"></i>

                            تقديم عرض

                        </h2>

                        <p class="text-muted">
                            قدم سعرك وتفاصيل تنفيذك للطلب
                        </p>
                    </div>


                    {{-- معلومات الطلب --}}
                    <div class="order-box">

                        <h5 class="order-title mb-3">

                            <i class="fa-solid fa-clipboard-list"></i>

                            معلومات الطلب

                        </h5>


                        <p class="order-info">

                            <strong>عنوان الطلب:</strong>

                            {{ $order->title }}

                        </p>


                        <p class="order-info">

                            <strong>الوصف:</strong>

                            {{ $order->description }}

                        </p>


                        <p class="order-info">

                            <strong>الميزانية:</strong>

                            <x-money :value="$order->budget" placeholder="غير محددة" />

                        </p>


                        <p class="order-info">

                            <strong>الموقع:</strong>

                            {{ $order->address ?? '-' }}

                        </p>


                        <p class="order-info">

                            <strong>التاريخ المطلوب:</strong>

                            {{ $order->service_date?->format('Y-m-d') ?? '-' }}

                        </p>

                    </div>


                    {{-- نموذج العرض --}}
                  <form action="{{ route('herafy.offer.store', ['order' => $order->id]) }}" method="POST">

                        @csrf

                        {{-- order_id مخفي --}}
                        <input type="hidden"
                               name="order_id"
                               value="{{ $order->id }}">


                        {{-- السعر --}}
                        <div class="mb-4">

                            <label class="form-label">

                                <i class="fa-solid fa-money-bill text-warning"></i>

                                السعر المقترح

                            </label>

                            <input
                                type="number"
                                name="price"
                                class="form-control"
                                step="0.01"
                                min="1"
                                value="{{ old('price') }}"
                                placeholder="مثال: 450"
                                required
                            >

                            @error('price')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- رسالة العرض --}}
                        <div class="mb-4">

                            <label class="form-label">

                                <i class="fa-solid fa-message text-warning"></i>

                                رسالة العرض

                            </label>

                            <textarea
                                name="message"
                                class="form-control"
                                rows="6"
                                placeholder="اكتب تفاصيل العرض ومدة التنفيذ..."
                                required
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- الأزرار --}}
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-gold">

                                <i class="fa-solid fa-paper-plane"></i>

                                إرسال العرض

                            </button>


                            <a href="{{ url()->previous() }}"
                               class="btn btn-secondary">

                                رجوع

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection