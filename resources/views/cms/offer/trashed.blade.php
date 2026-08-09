@extends('cms.parent')

@section('title','سلة محذوفات العروض')

@section('styles')

@endsection

@section('main_content')

سلة محذوفات العروض

@endsection

@section('content')

<div class="orders-card">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="page-title">

            <i class="fa-solid fa-trash"></i>

            سلة محذوفات العروض

        </h3>

        <a href="{{ route('offers_forceAll') }}">

            <button class="btn btn-danger">

                حذف نهائي من السلة

            </button>

        </a>

    </div>

    <div class="table-responsive">

        <table class="table table-hover">

            <thead>

                <tr>

                    <th>#</th>
                    <th>الطلب</th>
                    <th>الحرفي</th>
                    <th>السعر</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>

                </tr>

            </thead>

            <tbody>

                @forelse($offers as $offer)

                <tr>

                    <td>{{ $offer->id }}</td>

                    <td>{{ $offer->order->title ?? '-' }}</td>

                    <td>{{ $offer->craftman->user->name ?? '-' }}</td>

                    <td>{{ $offer->price }} ₪</td>

                    <td>

                        <span class="status {{ $offer->status }}">

                            @if($offer->status=='pending')

                                قيد الانتظار

                            @elseif($offer->status=='accepted')

                                مقبول

                            @elseif($offer->status=='in_progress')

                                قيد التنفيذ

                            @elseif($offer->status=='completed')

                                مكتمل

                            @else

                                ملغي

                            @endif

                        </span>

                    </td>

                    <td>{{ $offer->created_at->format('Y-m-d') }}</td>

                    <td>

                        <a href="{{ route('offers.show',$offer->id) }}"
                            class="btn btn-sm btn-info">

                            <i class="fa fa-eye"></i>

                        </a>

                        <a href="{{ route('offers_restore',$offer->id) }}"
                            class="btn btn-success btn-sm"
                            title="استرجاع">

                            <i class="fa-solid fa-rotate-left"></i>

                        </a>

                        <a href="{{ route('offers_force',$offer->id) }}"
                            class="btn btn-danger btn-sm"
                            title="حذف نهائي">

                            <i class="fa-solid fa-trash"></i>

                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7">

                        لا توجد عروض محذوفة

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection

@section('scripts')

@endsection