@extends('cms.parent')

@section('title','تفاصيل الطلب')

@section('styles')

<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }


    .details-card {

        background: #fff;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #f1f2f6;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .05);

    }


    .page-title {

        color: #0b1f3a;
        font-weight: 700;

    }


    .info-box {

        background: #f8f9fa;
        border-radius: 15px;
        padding: 18px;
        height: 100%;

    }


    .info-title {

        color: #0b1f3a;
        font-weight: 700;
        margin-bottom: 10px;

    }


    .info-value {

        color: #555;
        font-size: 15px;

    }



    .status {

        padding: 7px 18px;
        border-radius: 25px;
        font-weight: bold;
        display: inline-block;

    }


    .pending {

        background: #fff3cd;
        color: #856404;

    }


    .accepted {

        background: #d1ecf1;
        color: #0c5460;

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
        color: white;
        border-radius: 12px;
        padding: 10px 22px;

    }


    .btn-gold:hover {

        background: #b89625;
        color: white;

    }
</style>

@endsection



@section('content')


<div class="section-content">


    <div class="details-card">



        <div class="d-flex justify-content-between align-items-center mb-4">


            <h3 class="page-title">

                <i class="fa-solid fa-file-lines"></i>

                تفاصيل الطلب

            </h3>



            <a href="{{route('orders.index')}}"
                class="btn btn-secondary">

                رجوع

            </a>


        </div>





        <div class="row g-4">



            {{-- معلومات الزبون --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-user"></i>

                        معلومات الزبون

                    </h5>


                    <p class="info-value">

                        <strong>الاسم:</strong>

                        {{ $order->customer->user->name ?? '-' }}

                    </p>



                    <p class="info-value">

                        <strong>الهاتف:</strong>

                        {{ $order->customer->user->mobile ?? $order->phone }}

                    </p>



                    <p class="info-value">

                        <strong>رقم الهوية:</strong>

                        {{ $order->customer->id_number ?? '-' }}

                    </p>



                </div>


            </div>





            {{-- معلومات الموقع --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-location-dot"></i>

                        الموقع

                    </h5>


                    <p class="info-value">

                        <strong>المدينة:</strong>

                        {{ $order->city->name ?? '-' }}

                    </p>



                    <p class="info-value">

                        <strong>المنطقة:</strong>

                        {{ $order->area->name ?? '-' }}

                    </p>



                    <p class="info-value">

                        <strong>العنوان:</strong>

                        {{ $order->address ?? '-' }}

                    </p>


                </div>


            </div>







            {{-- تفاصيل الخدمة --}}

            <div class="col-12">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                        تفاصيل الخدمة

                    </h5>



                    <p class="info-value">

                        <strong>العنوان:</strong>

                        {{ $order->title }}

                    </p>



                    <p class="info-value">

                        <strong>الوصف:</strong>

                        {{ $order->description }}

                    </p>


                </div>


            </div>







            {{-- معلومات إضافية --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-circle-info"></i>

                        معلومات إضافية

                    </h5>



                    <p class="info-value">

                        <strong>الميزانية:</strong>

                        {{ $order->budget ?? 'غير محدد' }}

                    </p>



                    <p class="info-value">

                        <strong>التاريخ المطلوب:</strong>

                        {{ $order->service_date ?? '-' }}

                    </p>



                </div>


            </div>








            {{-- الحالة --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        الحالة

                    </h5>



                    <span class="status {{$order->status}}">


                        @if($order->status == 'pending')

                        قيد الانتظار

                        @elseif($order->status == 'accepted')

                        مقبول

                        @elseif($order->status == 'in_progress')

                        قيد التنفيذ

                        @elseif($order->status == 'completed')

                        مكتمل

                        @else

                        ملغي

                        @endif


                    </span>

@if($order->serviceJob)
    <div class="card mt-4">
        <div class="card-header">
            <h5>الخدمة</h5>
        </div>

        <div class="card-body">

            <p>
                <strong>حالة الخدمة:</strong>

                @if($order->serviceJob->status === 'pending')
                    <span class="badge bg-warning">قيد الانتظار</span>
                @elseif($order->serviceJob->status === 'in_progress')
                    <span class="badge bg-primary">قيد التنفيذ</span>
                @elseif($order->serviceJob->status === 'completed')
                    <span class="badge bg-success">مكتملة</span>
                @elseif($order->serviceJob->status === 'cancelled')
                    <span class="badge bg-danger">ملغاة</span>
                @endif
            </p>

            <a href="{{ route('service-jobs.show', $order->serviceJob) }}"
               class="btn btn-info btn-sm">
                عرض الخدمة
            </a>

        </div>
    </div>
@endif

                </div>


            </div>




        </div>



    </div>


</div>


@endsection