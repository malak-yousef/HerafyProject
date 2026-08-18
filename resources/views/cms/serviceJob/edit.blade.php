@extends('cms.parent')

@section('title', 'تعديل الخدمة')

@section('main_content', 'تعديل الخدمة')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5>
                <i class="fas fa-edit"></i>
                تعديل الخدمة #{{ $serviceJob->id }}
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('service-jobs.update', $serviceJob) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الطلب
                        </label>

                        <select name="order_id" class="form-select">

                            @foreach($orders as $order)

                                <option value="{{ $order->id }}"
                                    {{ old('order_id', $serviceJob->order_id) == $order->id ? 'selected' : '' }}>

                                    #{{ $order->id }}
                                    - {{ $order->title ?? 'طلب' }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            العرض
                        </label>

                        <select name="offer_id" class="form-select">

                            @foreach($offers as $offer)

                                <option value="{{ $offer->id }}"
                                    {{ old('offer_id', $serviceJob->offer_id) == $offer->id ? 'selected' : '' }}>

                                    عرض #{{ $offer->id }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            العميل
                        </label>

                        <select name="customers_id" class="form-select">

                            @foreach($customers as $customer)

                                <option value="{{ $customer->id }}"
                                    {{ old('customers_id', $serviceJob->customers_id) == $customer->id ? 'selected' : '' }}>

{{ $job->customer->user->name ?? '-' }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الحرفي
                        </label>

                        <select name="craftsmen_id" class="form-select">

                            @foreach($craftsmen as $craftsman)

                                <option value="{{ $craftsman->id }}"
                                    {{ old('craftsmen_id', $serviceJob->craftsmen_id) == $craftsman->id ? 'selected' : '' }}>

{{ $job->craftman->user->name ?? '-' }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            الحالة
                        </label>

                        <select name="status" class="form-select">

                            <option value="pending"
                                {{ old('status', $serviceJob->status) == 'pending' ? 'selected' : '' }}>
                                قيد الانتظار
                            </option>

                            <option value="in_progress"
                                {{ old('status', $serviceJob->status) == 'in_progress' ? 'selected' : '' }}>
                                قيد التنفيذ
                            </option>

                            <option value="completed"
                                {{ old('status', $serviceJob->status) == 'completed' ? 'selected' : '' }}>
                                مكتملة
                            </option>

                            <option value="cancelled"
                                {{ old('status', $serviceJob->status) == 'cancelled' ? 'selected' : '' }}>
                                ملغاة
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            تاريخ البدء
                        </label>

                        <input type="datetime-local"
                               name="started_at"
                               class="form-control"
                               value="{{ old(
                                   'started_at',
                                   $serviceJob->started_at?->format('Y-m-d\TH:i')
                               ) }}">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            تاريخ الإنجاز
                        </label>

                        <input type="datetime-local"
                               name="completed_at"
                               class="form-control"
                               value="{{ old(
                                   'completed_at',
                                   $serviceJob->completed_at?->format('Y-m-d\TH:i')
                               ) }}">

                    </div>

                </div>

                <button type="submit"
                        class="btn btn-success">

                    <i class="fas fa-save"></i>
                    تحديث

                </button>

                <a href="{{ route('service-jobs.index') }}"
                   class="btn btn-secondary">

                    إلغاء

                </a>

            </form>

        </div>

    </div>

</div>

@endsection