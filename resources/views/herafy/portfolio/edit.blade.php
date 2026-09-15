@extends('herafy.parent')
@section('title', 'تعديل العمل')
@section('content')
<main class="py-5 bg-light min-vh-100"><div class="container"><div class="row justify-content-center"><div class="col-xl-9">
    <div class="mb-4"><h1 class="h3 fw-bold">تعديل العمل</h1><p class="text-muted mb-0">حدّث تفاصيل المشروع أو صورة الغلاف.</p></div>
    <section class="card border-0 shadow-sm rounded-4"><div class="card-body p-4 p-lg-5">
        @if($portfolio->portfolioCover_image)<img src="{{ asset('storage/images/portfolio/'.$portfolio->portfolioCover_image) }}" alt="{{ $portfolio->title }}" class="img-fluid rounded-3 mb-4" style="max-height:260px;object-fit:cover">@endif
        <form method="POST" action="{{ route('herafy.craftman.portfolios.update', $portfolio) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('herafy.portfolio._form')</form>
    </div></section>
</div></div></div></main>
@endsection
