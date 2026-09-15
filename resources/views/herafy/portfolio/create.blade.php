@extends('herafy.parent')
@section('title', 'إضافة عمل جديد')
@section('content')
<main class="py-5 bg-light min-vh-100"><div class="container"><div class="row justify-content-center"><div class="col-xl-9">
    <div class="mb-4"><h1 class="h3 fw-bold">إضافة عمل إلى معرضك</h1><p class="text-muted mb-0">اعرض أفضل مشاريعك للزبائن بصورة ووصف واضحين.</p></div>
    <section class="card border-0 shadow-sm rounded-4"><div class="card-body p-4 p-lg-5">
        <form method="POST" action="{{ route('herafy.craftman.portfolios.store') }}" enctype="multipart/form-data">@csrf @include('herafy.portfolio._form')</form>
    </div></section>
</div></div></div></main>
@endsection
