@extends('cms.parent')

@section('title','الطلبات')

@section('styles')

<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }


    /* الكارد الرئيسي */
    .orders-card {
        background: #fff;
        border-radius: 18px;
        padding: 25px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .05);
        border: 1px solid #f1f1f1;
    }


    /* العنوان */
    .page-title {
        color: #0b1f3a;
        font-weight: 700;
    }


    /* الجدول */
    .table thead {
        background: #0b1f3a;
        color: white;
    }

    .table th {
        text-align: center;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }


    /* البادجات */
    .status {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
    }


    .pending {
        background: #fff3cd;
        color: #856404;
    }


    .accepted {
        background: #d1ecf1;
        color: #0c5460;
    }


    .completed {
        background: #d4edda;
        color: #155724;
    }


    .cancelled {
        background: #f8d7da;
        color: #721c24;
    }


    /* زر */
    .btn-gold {

        background: #d4af37;
        color: white;
        border-radius: 10px;
        padding: 8px 18px;
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


    <div class="orders-card">


        <div class="d-flex justify-content-between align-items-center mb-4">


            <h3 class="page-title">
                <i class="fa-solid fa-clipboard-list"></i>
                الطلبات
            </h3>


            <a href="{{route('orders.create')}}"
                class="btn btn-gold">

                <i class="fa-solid fa-plus"></i>
                إضافة طلب

            </a>

            <a href="{{ route('orders_trashed') }} ">
                <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">

                    سلة المحذوفات

                </button>
            </a>



        </div>



        <div class="table-responsive">

            <table class="table table-hover">


                <thead>

                    <tr>

                        <th>#</th>

                        <th>الزبون</th>

                        <th>العنوان</th>

                        <th>المدينة</th>

                        <th>المنطقة</th>

                        <th>الحالة</th>

                        <th>التاريخ</th>

                        <th>الإجراءات</th>


                    </tr>

                </thead>



                <tbody>


                    @forelse($orders as $order)


                    <tr>

                        <td>
                            {{$order->id}}
                        </td>


                        <td>
                            {{$order->customer->user->name ?? '-'}}
                        </td>


                        <td>
                            {{$order->title}}
                        </td>


                        <td>
                            {{$order->city->name ?? '-'}}
                        </td>


                        <td>
                            {{$order->area->name ?? '-'}}
                        </td>



                        <td>

                            <span class="status {{$order->status}}">

                                @if($order->status=='pending')
                                قيد الانتظار

                                @elseif($order->status=='accepted')
                                مقبول

                                @elseif($order->status=='completed')
                                مكتمل

                                @else
                                ملغي

                                @endif

                            </span>


                        </td>



                        <td>
                            {{$order->created_at->format('Y-m-d')}}
                        </td>



                        <td>
              <!--عند الضغط عليه، يفتح صفحة إنشاء عرض لهذا الطلب -->    
    <a href="{{ route('offers.create', ['order_id' => $order->id]) }}"
       class="btn btn-success btn-sm">
        <i class="fa-solid fa-hand-holding-dollar"></i>
        إضافة عرض
    </a>


                            <a href="{{route('orders.show',$order->id)}}"
                                class="btn btn-sm btn-info">

                                <i class="fa fa-eye"></i>

                            </a>



                            <a href="{{route('orders.edit',$order->id)}}"
                                class="btn btn-sm btn-warning">

                                <i class="fa fa-edit"></i>

                            </a>



                            <form
                                action="{{route('orders.destroy',$order->id)}}"
                                method="POST"
                                style="display:inline">

                                @csrf
                                @method('DELETE')

                                <button type="button"
                                    onclick="performDestroy({{$order->id}} , this)"
                                    class="action-btn delete-btn"
                                    title="نقل لسلة المحذوفات">
                                    <i class="fa-solid fa-trash"></i>
                                </button>


                            </form>



                        </td>


















                    </tr>



                    @empty

                    <tr>

                        <td colspan="8">

                            لا يوجد طلبات

                        </td>

                    </tr>

                    @endforelse


                </tbody>


            </table>

        </div>



        {{$orders->links()}}



    </div>


</div>


@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/orders/' + id, reference);
    }
</script>
@endsection