@extends('herafy.parent')

@section('title', 'طلبات العملاء')

@section('style')
@endsection

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">
                <i class="fas fa-clipboard-list text-warning"></i>
                طلبات العملاء
            </h2>

            <p class="text-muted">
                تصفح الطلبات المتاحة وقدم عروضك
            </p>
        </div>

    </div>


    <div class="row">

        @forelse($orders as $order)

            <div class="col-md-6 col-lg-4 mb-4">

                <div class="card h-100 shadow-sm border-0 rounded-4">

                    <div class="card-body">

                        <h5 class="fw-bold mb-3">
                            {{ $order->title }}
                        </h5>


                        <p class="text-muted">
                            {{ Str::limit($order->description, 100) }}
                        </p>


                        <hr>


                        <p>
                            <i class="fas fa-user text-warning"></i>

                            <strong>العميل:</strong>

                            {{ $order->customer->user->name ?? '-' }}
                        </p>


                        <p>
                            <i class="fas fa-map-marker-alt text-warning"></i>

                            <strong>الموقع:</strong>

                            {{ $order->city->name ?? '-' }}
                        </p>


                        <p>
                            <i class="fas fa-money-bill text-warning"></i>

                            <strong>الميزانية:</strong>

                            {{ $order->budget ?? 'غير محدد' }}
                            ₪
                        </p>


                        <p>
                            <i class="fas fa-calendar text-warning"></i>

                            <strong>التاريخ:</strong>

                            {{ $order->service_date ?? '-' }}
                        </p>


                        <a
                            href="{{ route('herafy.order.show', $order) }}"
                            class="btn btn-warning w-100 mt-2"
                        >
                            <i class="fas fa-eye"></i>
                            عرض الطلب
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="alert alert-info text-center">
                    لا توجد طلبات متاحة حاليًا.
                </div>

            </div>

        @endforelse

    </div>


    <div class="mt-4">
        {{ $orders->links() }}
    </div>

</div>

@endsection