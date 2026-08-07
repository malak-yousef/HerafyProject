@extends('cms.parent')

@section('title','تعديل الطلب')

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

            تعديل الطلب

        </h3>



        <form action="{{route('orders.update',$order->id)}}" method="POST">


            @csrf

            @method('PUT')



            <div class="row">



                {{-- الزبون --}}

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        الزبون
                    </label>


                    <select name="customer_id"
                        class="form-select">


                        @foreach($customers as $customer)

                        <option value="{{$customer->id}}"

                            @if($order->customer_id == $customer->id)
                            selected
                            @endif

                            >

                            {{$customer->user->name}}

                        </option>


                        @endforeach


                    </select>

                </div>





                {{-- العنوان --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        عنوان الطلب
                    </label>


                    <input type="text"
                        name="title"
                        class="form-control"

                        value="{{$order->title}}">


                </div>





                {{-- المدينة --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        المدينة
                    </label>


                    <select name="city_id"
                        class="form-select">


                        @foreach($cities as $city)

                        <option value="{{$city->id}}"

                            @if($order->city_id == $city->id)
                            selected
                            @endif

                            >

                            {{$city->name}}

                        </option>


                        @endforeach


                    </select>


                </div>






                {{-- المنطقة --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        المنطقة
                    </label>


                    <select name="area_id"
                        class="form-select">


                        @foreach($areas as $area)

                        <option value="{{$area->id}}"

                            @if($order->area_id == $area->id)
                            selected
                            @endif

                            >

                            {{$area->name}}

                        </option>


                        @endforeach


                    </select>


                </div>







                {{-- الميزانية --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        الميزانية
                    </label>


                    <input type="number"
                        name="budget"
                        class="form-control"

                        value="{{$order->budget}}">


                </div>







                {{-- التاريخ --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        التاريخ المطلوب
                    </label>


                    <input type="date"
                        name="service_date"
                        class="form-control"

                        value="{{$order->service_date}}">


                </div>







                {{-- الهاتف --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        الهاتف
                    </label>


                    <input type="text"
                        name="phone"
                        class="form-control"

                        value="{{$order->phone}}">


                </div>







                {{-- العنوان --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        العنوان بالتفصيل
                    </label>


                    <input type="text"
                        name="address"
                        class="form-control"

                        value="{{$order->address}}">


                </div>







                {{-- الحالة --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">
                        حالة الطلب
                    </label>


                    <select name="status"
                        class="form-select">


                        <option value="pending"
                            @if($order->status=='pending') selected @endif>

                            قيد الانتظار

                        </option>



                        <option value="accepted"
                            @if($order->status=='accepted') selected @endif>

                            مقبول

                        </option>




                        <option value="in_progress"
                            @if($order->status=='in_progress') selected @endif>

                            قيد التنفيذ

                        </option>




                        <option value="completed"
                            @if($order->status=='completed') selected @endif>

                            مكتمل

                        </option>




                        <option value="cancelled"
                            @if($order->status=='cancelled') selected @endif>

                            ملغي

                        </option>


                    </select>


                </div>







                {{-- الوصف --}}

                <div class="col-12 mb-3">


                    <label class="form-label">
                        الوصف
                    </label>


                    <textarea
                        name="description"
                        rows="5"
                        class="form-control">{{$order->description}}</textarea>


                </div>




            </div>





            <button class="btn btn-gold">

                <i class="fa fa-save"></i>

                حفظ التعديل

            </button>



            <a href="{{route('orders.index')}}"
                class="btn btn-secondary">

                رجوع

            </a>




        </form>


    </div>


</div>


@endsection