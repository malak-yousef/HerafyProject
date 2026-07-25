@extends('cms.parent')

@section('title','طلبات الزبون')

@section('main_content','طلبات الزبون')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header d-flex justify-content-between align-items-center">

            <div>
                <h4 class="mb-0">
                    طلبات الزبون :
                    <span class="text-warning">
                        {{ $customer->user->name }}
                    </span>
                </h4>
            </div>

            <a href="{{ route('customers.index') }}"
                class="btn btn-secondary">

                رجوع

            </a>

        </div>

        <div class="card-body">

            @if($orders->count())

            <table class="table table-bordered table-hover text-center align-middle">

                <thead class="table-warning">

                    <tr>

                        <th>#</th>

                        <th>العنوان</th>

                        <th>المدينة</th>

                        <th>المنطقة</th>

                        <th>الميزانية</th>

                        <th>الحالة</th>

                        <th>تاريخ الإنشاء</th>

                        <th>الإجراءات</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($orders as $order)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $order->title }}</td>

                        <td>{{ $order->city->name }}</td>

                        <td>{{ $order->area->name }}</td>

                        <td>{{ $order->budget }} ₪</td>

                        <td>

                            @switch($order->status)

                            @case('pending')

                            <span class="badge bg-warning">
                                قيد الانتظار
                            </span>

                            @break

                            @case('accepted')

                            <span class="badge bg-success">
                                مقبول
                            </span>

                            @break

                            @case('completed')

                            <span class="badge bg-primary">
                                مكتمل
                            </span>

                            @break

                            @case('cancelled')

                            <span class="badge bg-danger">
                                ملغي
                            </span>

                            @break

                            @default

                            <span class="badge bg-secondary">
                                {{ $order->status }}
                            </span>

                            @endswitch

                        </td>

                        <td>

                            {{ $order->created_at->format('Y-m-d') }}

                        </td>

                        <td>

                            <a href="{{ route('orders.show',$order->id) }}"
                                class="btn btn-sm btn-info">

                                <i class="fas fa-eye"></i>

                            </a>

                            <a href="{{ route('orders.edit',$order->id) }}"
                                class="btn btn-sm btn-warning">

                                <i class="fas fa-edit"></i>

                            </a>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

            {{ $orders->links() }}

            @else

            <div class="alert alert-info text-center">

                لا يوجد طلبات لهذا الزبون

            </div>

            @endif

        </div>

    </div>

</div>

@endsection