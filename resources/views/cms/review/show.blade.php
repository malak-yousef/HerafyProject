@extends('cms.parent')

@section('title','تفاصيل التقييم')

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


    .approved {

        background: #d1e7dd;
        color: #0f5132;

    }


    .rejected {

        background: #f8d7da;
        color: #842029;

    }


    .stars {

        color: #f1c40f;
        font-size: 22px;

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

                <i class="fa-solid fa-star"></i>

                تفاصيل التقييم

            </h3>



            <a href="{{route('reviews.index')}}"
                class="btn btn-secondary">

                رجوع

            </a>


        </div>





        <div class="row g-4">



            {{-- معلومات الحرفي --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-user-gear"></i>

                        الحرفي

                    </h5>


                    <p class="info-value">

                        <strong>الاسم:</strong>

                        {{ $review->craftsman->user->name ?? '-' }}

                    </p>



                </div>


            </div>





            {{-- معلومات الزبون --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-user"></i>

                        الزبون

                    </h5>


                    <p class="info-value">

                        <strong>الاسم:</strong>

                        {{ $review->customer->user->name ?? '-' }}

                    </p>



                </div>


            </div>





            {{-- تفاصيل التقييم --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-clipboard-list"></i>

                        الطلب

                    </h5>


                    <p class="info-value">

                        <strong>عنوان الطلب:</strong>

                        {{ $review->request->title ?? '-' }}

                    </p>



                </div>


            </div>





            {{-- التقييم --}}

            <div class="col-md-6">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-star"></i>

                        التقييم

                    </h5>


                    <p class="info-value">

                        <span class="stars">

                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fa-solid fa-star"></i>
                                @else
                                    <i class="fa-regular fa-star"></i>
                                @endif
                            @endfor

                        </span>

                        <span class="ms-2">({{ $review->rating }} / 5)</span>

                    </p>



                </div>


            </div>





            {{-- التعليق --}}

            <div class="col-12">


                <div class="info-box">


                    <h5 class="info-title">

                        <i class="fa-solid fa-comment"></i>

                        التعليق

                    </h5>


                    <p class="info-value">

                        {{ $review->comment ?? 'لا يوجد تعليق' }}

                    </p>


                </div>


            </div>





            {{-- الحالة --}}

            <div class="col-12">


                <div class="info-box">


                    <h5 class="info-title">

                        الحالة

                    </h5>



                    <span class="status {{$review->status}}">


                        @if($review->status == 'pending')

                        قيد الانتظار

                        @elseif($review->status == 'approved')

                        مقبول

                        @else

                        مرفوض

                        @endif


                    </span>



                </div>


            </div>




        </div>



    </div>


</div>


@endsection
