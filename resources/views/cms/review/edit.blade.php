@extends('cms.parent')

@section('title','تعديل التقييم')

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
</style>

@endsection



@section('content')


<div class="section-content">


    <div class="form-card">


        <h3 class="form-title">

            <i class="fa-solid fa-pen"></i>

            تعديل التقييم

        </h3>



        <form action="{{route('reviews.update',$review->id)}}" method="POST">


            @csrf

            @method('PUT')



            <div class="row">



                {{-- الحرفي --}}

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        الحرفي
                    </label>


                    <select name="craftsman_id"
                        class="form-select">


                        @foreach($craftmen as $craftman)

                        <option value="{{$craftman->id}}"

                            @if($review->craftsman_id == $craftman->id)
                            selected
                            @endif

                            >

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


                        @foreach($customers as $customer)

                        <option value="{{$customer->id}}"

                            @if($review->customer_id == $customer->id)
                            selected
                            @endif

                            >

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


                        @foreach($requests as $request)

                        <option value="{{$request->id}}"

                            @if($review->request_id == $request->id)
                            selected
                            @endif

                            >

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


                        @for($i = 1; $i <= 5; $i++)

                        <option value="{{$i}}"

                            @if($review->rating == $i)
                            selected
                            @endif

                            >

                            {{$i}} {{ $i == 1 ? 'نجمة' : ($i == 2 ? 'نجمتان' : ($i == 3 ? '3 نجوم' : ($i == 4 ? '4 نجوم' : '5 نجوم'))) }}

                        </option>

                        @endfor


                    </select>


                </div>



                {{-- الحالة --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        حالة التقييم
                    </label>


                    <select name="status"
                        class="form-select">


                        <option value="pending"
                            @if($review->status=='pending') selected @endif>

                            قيد الانتظار

                        </option>



                        <option value="approved"
                            @if($review->status=='approved') selected @endif>

                            مقبول

                        </option>




                        <option value="rejected"
                            @if($review->status=='rejected') selected @endif>

                            مرفوض

                        </option>


                    </select>


                </div>



                {{-- التعليق --}}

                <div class="col-12 mb-3">


                    <label class="form-label">
                        التعليق
                    </label>


                    <textarea
                        name="comment"
                        rows="5"
                        class="form-control">{{$review->comment}}</textarea>


                </div>




            </div>





            <button class="btn btn-gold">

                <i class="fa fa-save"></i>

                حفظ التعديل

            </button>



            <a href="{{route('reviews.index')}}"
                class="btn btn-secondary">

                رجوع

            </a>




        </form>


    </div>


</div>


@endsection
