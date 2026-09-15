@extends('cms.parent')

@section('title','التقييمات')

@section('styles')

<style>
    .legacy-review-action, .legacy-review-action + form { display: none !important; }
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

            <a href="{{ route('reviews_trashed') }} ">
                <button class="btn btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#addCategoryModal">

                    سلة المحذوفات

                </button>
            </a>



        </div>



        <div class="table-responsive">

            <form method="GET" class="row g-2 mb-3 align-items-end"><div class="col-md-3"><label class="form-label">بحث</label><input name="search" value="{{ request('search') }}" class="form-control" maxlength="100"></div><div class="col-md-2"><label class="form-label">الحالة</label><select name="status" class="form-select"><option value="">الكل</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select></div><div class="col-md-2"><label class="form-label">التقييم</label><select name="rating" class="form-select"><option value="">الكل</option>@foreach(range(1,5) as $rating)<option value="{{ $rating }}" @selected((string)request('rating') === (string)$rating)>{{ $rating }}</option>@endforeach</select></div><div class="col-md-3"><label class="form-label">الحرفي</label><select name="craftman_id" class="form-select"><option value="">الكل</option>@foreach($craftmen as $craftman)<option value="{{ $craftman->id }}" @selected((string)request('craftman_id') === (string)$craftman->id)>{{ $craftman->user->name ?? $craftman->id }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-primary" type="submit">بحث</button> <a href="{{ route('reviews.index') }}">إعادة ضبط</a></div></form>
            <div class="mb-3"><form method="GET" class="row g-2"><div class="col-md-3"><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div><div class="col-md-3"><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div><div class="col-md-3"><select name="sort" class="form-select"><option value="newest">الأحدث</option><option value="oldest">الأقدم</option><option value="rating_desc">الأعلى تقييمًا</option><option value="rating_asc">الأقل تقييمًا</option></select></div><div class="col-md-3"><button class="btn btn-outline-primary" type="submit">تطبيق</button></div></form></div><table class="table table-hover">


                <thead>

                    <tr>

                        <th>#</th>

                        <th>الحرفي</th>

                        <th>الزبون</th>

                        <th>الطلب</th>

                        <th>التقييم</th>

                        <th>التعليق</th>

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
                            {{ Str::limit($review->comment ?? '-', 30) }}
                        </td>



                        <td>

                            <x-cms.status-badge :status="$review->status" />
                            <span class="status {{$review->status}} legacy-review-status">

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
                                class="btn btn-sm btn-info legacy-review-action">

                                <i class="fa fa-eye"></i>

                            </a>



                            <a href="{{route('reviews.edit',$review->id)}}"
                                class="btn btn-sm btn-warning legacy-review-action">

                                <i class="fa fa-edit"></i>

                            </a>



                            <x-cms.table-actions :show-url="route('reviews.show', $review->id)" :edit-url="route('reviews.edit', $review->id)" :delete-url="route('reviews.destroy', $review->id)" show-permission="reviews.view" edit-permission="reviews.update" delete-permission="reviews.delete" :delete-id="$review->id" />
                            <form
                                action="{{route('reviews.destroy',$review->id)}}"
                                method="POST"
                                style="display:inline">

                                @csrf
                                @method('DELETE')

                                <button type="button"
                                    onclick="performDestroy({{$review->id}} , this)"
                                    class="action-btn delete-btn"
                                    title="نقل لسلة المحذوفات">
                                    <i class="fa-solid fa-trash"></i>
                                </button>


                            </form>



                        </td>

                    </tr>



                    @empty

                    <tr>

                        <td colspan="9"><x-cms.empty-state :title="request()->hasAny(['search','status','rating','craftman_id','date_from','date_to','sort']) ? 'لا توجد نتائج مطابقة لخيارات البحث.' : 'لا توجد تقييمات حتى الآن'" :reset-url="route('reviews.index')" />

                            لا توجد تقييمات

                        </td>

                    </tr>

                    @endforelse


                </tbody>


            </table>

        </div>



        {{$reviews->links()}}



    </div>


</div>


@endsection

@section('scripts')
<script>
    function performDestroy(id, reference) {
        confirmDestroy('/cms/admin/reviews/' + id, reference);
    }
</script>
@endsection
