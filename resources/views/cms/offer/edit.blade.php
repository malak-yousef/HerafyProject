@extends('cms.parent')

@section('title','تعديل عرض')

@section('styles')
@endsection

@section('content')

<div class="form-card">

    <h3 class="form-title">
        <i class="fa-solid fa-pen-to-square"></i>
        تعديل العرض
    </h3>

    <form action="{{ route('offers.update',$offer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- الطلب --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الطلب</label>

                <select name="order_id" class="form-select">

                    @foreach($orders as $order)
                        <option value="{{ $order->id }}"
                            {{ $offer->order_id == $order->id ? 'selected' : '' }}>
                            {{ $order->title }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- الحرفي --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الحرفي</label>

                <select name="craftman_id" class="form-select">

                    @foreach($craftmen as $craftman)
                        <option value="{{ $craftman->id }}"
                            {{ $offer->craftman_id == $craftman->id ? 'selected' : '' }}>
                            {{ $craftman->user->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- السعر --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">السعر</label>

                <input
                    type="number"
                    step="0.01"
                    name="price"
                    value="{{ $offer->price }}"
                    class="form-control">
            </div>

            {{-- الحالة --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الحالة</label>

                <select name="status" class="form-select">

                    <option value="pending" {{ $offer->status=='pending'?'selected':'' }}>
                        قيد الانتظار
                    </option>

                    <option value="accepted" {{ $offer->status=='accepted'?'selected':'' }}>
                        مقبول
                    </option>

                    <option value="in_progress" {{ $offer->status=='in_progress'?'selected':'' }}>
                        قيد التنفيذ
                    </option>

                    <option value="completed" {{ $offer->status=='completed'?'selected':'' }}>
                        مكتمل
                    </option>

                    <option value="cancelled" {{ $offer->status=='cancelled'?'selected':'' }}>
                        ملغي
                    </option>

                </select>
            </div>

            {{-- الرسالة --}}
            <div class="col-12 mb-3">

                <label class="form-label">رسالة العرض</label>

                <textarea
                    name="message"
                    rows="5"
                    class="form-control">{{ $offer->message }}</textarea>

            </div>

        </div>

        <div class="mt-3">

            <button class="btn btn-gold">
                <i class="fa fa-save"></i>
                تحديث
            </button>

            <a href="{{ route('offers.index') }}" class="btn btn-secondary">
                رجوع
            </a>

        </div>

    </form>

</div>

@endsection