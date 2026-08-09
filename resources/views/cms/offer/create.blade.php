@extends('cms.parent')

@section('title','إضافة عرض')

@section('styles')
@endsection

@section('content')

<div class="form-card">

    <h3 class="form-title">
        <i class="fa-solid fa-plus"></i>
        إضافة عرض جديد
    </h3>

    <form action="{{ route('offers.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- الطلب --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الطلب</label>

                <select name="order_id" class="form-select">
                    <option value="">اختر الطلب</option>

                    @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            {{ $order->title }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- الحرفي --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الحرفي</label>

                <select name="craftman_id" class="form-select">
                    <option value="">اختر الحرفي</option>

                    @foreach($craftmen as $craftman)
                        <option value="{{ $craftman->id }}" {{ old('craftman_id') == $craftman->id ? 'selected' : '' }}>
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
                    name="price"
                    step="0.01"
                    class="form-control"
                    value="{{ old('price') }}"
                    placeholder="أدخل السعر">
            </div>

            {{-- الحالة --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">الحالة</label>

                <select name="status" class="form-select">

                    <option value="pending" {{ old('status')=='pending' ? 'selected':'' }}>
                        قيد الانتظار
                    </option>

                    <option value="accepted" {{ old('status')=='accepted' ? 'selected':'' }}>
                        مقبول
                    </option>

                    <option value="in_progress" {{ old('status')=='in_progress' ? 'selected':'' }}>
                        قيد التنفيذ
                    </option>

                    <option value="completed" {{ old('status')=='completed' ? 'selected':'' }}>
                        مكتمل
                    </option>

                    <option value="cancelled" {{ old('status')=='cancelled' ? 'selected':'' }}>
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
                    class="form-control"
                    placeholder="اكتب تفاصيل العرض">{{ old('message') }}</textarea>

            </div>

        </div>

        <div class="mt-3">

            <button class="btn btn-gold">
                <i class="fa fa-save"></i>
                حفظ العرض
            </button>

            <a href="{{ route('offers.index') }}" class="btn btn-secondary">
                رجوع
            </a>

        </div>

    </form>

</div>

@endsection