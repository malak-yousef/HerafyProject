@extends('cms.parent')

@section('title','التقييمات')

@section('styles')

<style>
    .section-content {
        font-family: 'Cairo', sans-serif;
    }


    /* الكارد الرئيسي */
    .reviews-card {
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


    .approved {
        background: #d4edda;
        color: #155724;
    }


    .rejected {
        background: #f8d7da;
        color: #721c24;
    }


    /* النجوم */
    .stars {
        color: #f1c40f;
        font-size: 14px;
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



@section('main_content')

سلة محذوفات

@endsection

@section('content')

<div class="section-content">


    <div class="reviews-card">


        <div class="d-flex justify-content-between align-items-center mb-4">


            <h3 class="page-title">
                <i class="fa-solid fa-star"></i>
                التقييمات
            </h3>


            <a href="{{route('reviews.create')}}"
                class="btn btn-gold">

                <i class="fa-solid fa-plus"></i>
                إضافة تقييم

            </a>

            <a href="{{ route('reviews_forceAll') }} ">
                <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">
                    حذف نهائي من سلة المحذوفات
                </button>
            </a>

        </div>



        <div class="table-responsive">

            <table class="table table-hover">


                <thead>

                    <tr>

                        <th>#</th>

                        <th>الحرفي</th>

                        <th>الزبون</th>

                        <th>الطلب</th>

                        <th>التقييم</th>

                        <th>الحالة</th>

                        <th>التاريخ</th>

                        <th>الإجراءات</th>


                    </tr>

                </thead>



                <tbody>


                    @forelse($reviews as $review)


                    <tr>

                        <td>
                            {{$review->id}}
                        </td>


                        <td>
                            {{$review->craftsman->user->name ?? '-'}}
                        </td>


                        <td>
                            {{$review->customer->user->name ?? '-'}}
                        </td>


                        <td>
                            {{$review->request->title ?? '-'}}
                        </td>


                        <td>

                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fa-solid fa-star"></i>
                                    @else
                                        <i class="fa-regular fa-star"></i>
                                    @endif
                                @endfor
                            </span>

                        </td>



                        <td>

                            <span class="status {{$review->status}}">

                                @if($review->status=='pending')
                                قيد الانتظار

                                @elseif($review->status=='approved')
                                مقبول

                                @else
                                مرفوض

                                @endif

                            </span>


                        </td>



                        <td>
                            {{$review->created_at->format('Y-m-d')}}
                        </td>



                        <td>


                            <a href="{{route('reviews.show',$review->id)}}"
                                class="btn btn-sm btn-info">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ route('reviews_restore', $review->id) }}"
                                class="action-btn btn btn-danger"
                                title="استرجاع">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>





                            <!-- زر الحذف Delete -->
                            <form action="{{ route('reviews.destroy', $review->id) }}"
                                method="POST"
                                style="display:inline-block">

                                @csrf
                                @method('DELETE')


                                <a href="{{ route('reviews_force', $review->id) }}"
                                    class="action-btn btn btn-danger"
                                    title="حذف نهائي"
                                    type="button">

                                    <i class="fa-solid fa-trash"></i>
                                </a>

                            </form>


                        </td>


                    </tr>



                    @empty

                    <tr>

                        <td colspan="8">

                            لا توجد تقييمات

                        </td>

                    </tr>

                    @endforelse


                </tbody>


            </table>

        </div>







    </div>


</div>


@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/reviews_force/' + id, reference);
    }
</script>
@endsection
