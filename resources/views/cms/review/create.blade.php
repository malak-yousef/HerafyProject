@extends('cms.parent')

@section('title','إضافة تقييم')

@section('styles')

<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }

    .form-card {

        background: #fff;
        border-radius: 18px;
        padding: 30px;
        border: 1px solid #f1f2f6;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .05);

    }


    .form-title {

        color: #0b1f3a;
        font-weight: 700;
        margin-bottom: 25px;

    }


    .form-label {

        font-weight: 600;
        color: #343a40;

    }


    .form-control,
    .form-select {

        border-radius: 12px;
        padding: 12px;

    }


    .btn-gold {

        background: #d4af37;
        color: white;
        border-radius: 12px;
        padding: 10px 25px;
        border: none;

    }


    .btn-gold:hover {

        background: #b89625;
        color: white;

    }


    .btn-secondary {

        border-radius: 12px;

    }
</style>

@endsection



@section('content')


<div class="section-content">


    <div class="form-card">


        <h3 class="form-title">

            <i class="fa-solid fa-plus"></i>

            إضافة تقييم جديد

        </h3>



        <form action="{{route('reviews.store')}}" method="POST">

            @csrf



            <div class="row">



                {{-- الحرفي --}}

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        الحرفي
                    </label>


                    <select name="craftsman_id"
                        class="form-select">


                        <option value="">
                            اختر الحرفي
                        </option>


                        @foreach($craftmen as $craftman)

                        <option value="{{$craftman->id}}">

                            {{$craftman->user->name ?? 'حرفي'}}

                        </option>


                        @endforeach


                    </select>


                </div>



                {{-- الزبون --}}

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        الزبون
                    </label>


                    <select name="customer_id"
                        class="form-select">


                        <option value="">
                            اختر الزبون
                        </option>


                        @foreach($customers as $customer)

                        <option value="{{$customer->id}}">

                            {{$customer->user->name ?? 'زبون'}}

                        </option>


                        @endforeach


                    </select>


                </div>



                {{-- الطلب --}}

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        الطلب
                    </label>


                    <select name="request_id"
                        class="form-select">


                        <option value="">
                            اختر الطلب
                        </option>


                        @foreach($requests as $request)

                        <option value="{{$request->id}}">

                            {{$request->title}} ({{ $request->customer->user->name ?? '-' }})

                        </option>


                        @endforeach


                    </select>


                </div>



                {{-- التقييم --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">

                        التقييم (1 - 5)

                    </label>


                    <select name="rating"
                        class="form-select">


                        <option value="">
                            اختر التقييم
                        </option>


                        @for($i = 1; $i <= 5; $i++)

                            <option value="{{$i}}">

                            {{$i}} {{ $i == 1 ? 'نجمة' : ($i == 2 ? 'نجمتان' : ($i == 3 ? '3 نجوم' : ($i == 4 ? '4 نجوم' : '5 نجوم'))) }}

                            </option>

                            @endfor


                    </select>


                </div>



                {{-- التعليق --}}

                <div class="col-12 mb-3">


                    <label class="form-label">

                        التعليق

                    </label>


                    <textarea
                        name="comment"
                        class="form-control"
                        rows="5"
                        placeholder="اكتب ملاحظاتك على الخدمة المقدمة">

                    </textarea>


                </div>




            </div>



            <div class="mt-3">


                <button class="btn btn-gold">

                    <i class="fa fa-save"></i>

                    حفظ التقييم

                </button>



                <a href="{{route('reviews.index')}}"
                    class="btn btn-secondary">

                    رجوع

                </a>


            </div>




        </form>


    </div>


</div>


@endsection