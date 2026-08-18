@extends('cms.parent')

@section('title','العروض')

@section('styles')

@endsection

@section('content')

<div class="orders-card">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="page-title">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            العروض
        </h3>

        <a href="{{ route('offers.create') }}"
            class="btn btn-gold">

            <i class="fa-solid fa-plus"></i>
            إضافة عرض

        </a>

        <a href="{{ route('offers_trashed') }}">
            <button class="btn btn-danger">

                سلة المحذوفات

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

                    <td>
                        {{ $offer->id }}
                    </td>

                    <td>
                        {{ $offer->order->title ?? '-' }}
                    </td>

                    <td>
                        {{ $offer->craftman->user->name ?? '-' }}
                    </td>

                    <td>
                        {{ $offer->price }} ₪
                    </td>

                    <td>

                        <span class="status {{ $offer->status }}">

                            @if($offer->status == 'pending')

                                قيد الانتظار

                            @elseif($offer->status == 'accepted')

                                مقبول

                            @elseif($offer->status == 'in_progress')

                                قيد التنفيذ

                            @elseif($offer->status == 'completed')

                                مكتمل

                            @else

                                ملغي

                            @endif

                        </span>

                    </td>

                    <td>
                        {{ $offer->created_at->format('Y-m-d') }}
                    </td>

                    <td>

                        <a href="{{ route('offers.show',$offer->id) }}"
                            class="btn btn-sm btn-info">

                            <i class="fa fa-eye"></i>

                        </a>

                        <a href="{{ route('offers.edit',$offer->id) }}"
                            class="btn btn-sm btn-warning">

                            <i class="fa fa-edit"></i>

                        </a>

                        <form
                            action="{{ route('offers.destroy',$offer->id) }}"
                            method="POST"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button type="button"
                                onclick="performDestroy({{ $offer->id }}, this)"
                                class="action-btn delete-btn"
                                title="نقل لسلة المحذوفات">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="7">

                        لا يوجد عروض

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{ $offers->links() }}

</div>

@endsection

@section('scripts')

@endsection