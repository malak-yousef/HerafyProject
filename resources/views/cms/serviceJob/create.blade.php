@extends('cms.parent')

@section('title', 'إضافة خدمة')

@section('main_content', 'إضافة خدمة')

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5>
                <i class="fas fa-plus"></i>
                إضافة خدمة جديدة
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('service-jobs.store') }}"
                  method="POST">

                @csrf
                @if ($errors->any())
    <div class="alert alert-danger">
        <strong>يوجد أخطاء:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                <div class="row">

                    {{-- Order --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الطلب
                        </label>

                        <select name="order_id"
                                class="form-select @error('order_id') is-invalid @enderror">

                            <option value="">
                                اختر الطلب
                            </option>

                            @foreach($orders as $order)

                                <option value="{{ $order->id }}"
                                    {{ old('order_id') == $order->id ? 'selected' : '' }}>

                                    #{{ $order->id }}
                                    - {{ $order->title ?? 'طلب' }}

                                </option>

                            @endforeach

                        </select>

                        @error('order_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Offer --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            العرض
                        </label>

                        <select name="offer_id"
                                class="form-select">

                            <option value="">
                                اختر العرض
                            </option>

                            @foreach($offers as $offer)

                                <option value="{{ $offer->id }}"
                                    {{ old('offer_id') == $offer->id ? 'selected' : '' }}>

                                    عرض #{{ $offer->id }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Customer --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            العميل
                        </label>

                        <select name="customer_id"
                                class="form-select">

                            <option value="">
                                اختر العميل
                            </option>

                            @foreach($customers as $customer)

                                <option value="{{ $customer->id }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>

                                    {{ $customer->user->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Craftsman --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الحرفي
                        </label>

                        <select name="craftman_id"
                                class="form-select">

                            <option value="">
                                اختر الحرفي
                            </option>

                            @foreach($craftsmen as $craftsman)

                                <option value="{{ $craftsman->id }}"
                                    {{ old('craftman_id') == $craftsman->id ? 'selected' : '' }}>

                                    {{ $craftsman->user->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الحالة
                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="pending">
                                قيد الانتظار
                            </option>

                            <option value="in_progress">
                                قيد التنفيذ
                            </option>

                            <option value="completed">
                                مكتملة
                            </option>

                            <option value="cancelled">
                                ملغاة
                            </option>

                        </select>

                    </div>


                    {{-- Started At --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            تاريخ البدء
                        </label>

                        <input type="datetime-local"
                               name="started_at"
                               class="form-control"
                               value="{{ old('started_at') }}">

                    </div>


                    {{-- Completed At --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            تاريخ الإنجاز
                        </label>

                        <input type="datetime-local"
                               name="completed_at"
                               class="form-control"
                               value="{{ old('completed_at') }}">

                    </div>

                </div>

                <div class="mt-3">

                    <button type="submit"
                            class="btn btn-success">

                        <i class="fas fa-save"></i>
                        حفظ

                    </button>

                    <a href="{{ route('service-jobs.index') }}"
                       class="btn btn-secondary">

                        إلغاء

                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection