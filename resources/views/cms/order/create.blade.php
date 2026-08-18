@extends('cms.parent')

@section('title','إضافة طلب')

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

            إضافة طلب جديد

        </h3>



        <form action="{{route('orders.store')}}" method="POST">

            @csrf



            <div class="row">



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
                        placeholder="مثال: تصليح كهرباء المنزل">


                </div>





                {{-- المدينة --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">

                        المدينة

                    </label>


                    <select name="city_id"
                        class="form-select">


                        <option value="">
                            اختر المدينة
                        </option>


                        @foreach($cities as $city)

                        <option value="{{$city->id}}">

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


                        <option value="">
                            اختر المنطقة
                        </option>


                        @foreach($areas as $area)

                        <option value="{{$area->id}}">

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
                        placeholder="مثال: 100">


                </div>





                {{-- التاريخ --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">

                        التاريخ المطلوب

                    </label>


                    <input type="date"
                        name="service_date"
                        class="form-control">


                </div>





                {{-- الهاتف --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">

                        رقم التواصل

                    </label>


                    <input type="text"
                        name="phone"
                        class="form-control">


                </div>





                {{-- العنوان بالتفصيل --}}

                <div class="col-md-6 mb-3">


                    <label class="form-label">

                        العنوان

                    </label>


                    <input type="text"
                        name="address"
                        class="form-control">


                </div>





                {{-- الوصف --}}

                <div class="col-12 mb-3">


                    <label class="form-label">

                        وصف الطلب

                    </label>


                    <textarea
                        name="description"
                        class="form-control"
                        rows="5"
                        placeholder="اكتب تفاصيل الخدمة المطلوبة">

</textarea>


                </div>




            </div>



            <div class="mt-3">


                <button class="btn btn-gold">

                    <i class="fa fa-save"></i>

                    حفظ الطلب

                </button>



                <a href="{{route('orders.index')}}"
                    class="btn btn-secondary">

                    رجوع

                </a>
               

            </div>




        </form>


    </div>


</div>


@endsection