@extends('herafy.parent')
@section('title', 'معرض أعمالي')
@section('style')<style>.portfolio-card{border:0;border-radius:18px;overflow:hidden}.portfolio-cover{width:100%;height:220px;object-fit:cover}.portfolio-placeholder{height:220px;background:#fff3cd;color:#8a6200;display:flex;align-items:center;justify-content:center;font-size:3rem}</style>@endsection
@section('content')
<main class="py-5 bg-light min-vh-100"><div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h1 class="h3 fw-bold mb-1">معرض أعمالي</h1><p class="text-muted mb-0">أضف مشاريعك السابقة ليشاهدها الزبائن.</p></div><a href="{{ route('herafy.craftman.portfolios.create') }}" class="btn btn-warning fw-bold"><i class="fas fa-plus ms-1"></i> إضافة عمل جديد</a></div>
    <div class="row g-4">
        @forelse($portfolios as $portfolio)
            <div class="col-md-6 col-xl-4"><article class="card portfolio-card shadow-sm h-100">
                @if($portfolio->portfolioCover_image)<img src="{{ asset('storage/images/portfolio/'.$portfolio->portfolioCover_image) }}" alt="{{ $portfolio->title }}" class="portfolio-cover" width="220" height="220" loading="lazy" decoding="async">@else<div class="portfolio-placeholder"><i class="fas fa-images"></i></div>@endif
                <div class="card-body p-4"><div class="d-flex justify-content-between gap-2"><h2 class="h5 fw-bold">{{ $portfolio->title }}</h2><span class="badge text-bg-{{ $portfolio->status ? 'success' : 'secondary' }}">{{ $portfolio->status ? 'ظاهر' : 'مخفي' }}</span></div><p class="text-muted">{{ Str::limit($portfolio->description, 120) }}</p><div class="small text-muted mb-3">@if($portfolio->location)<i class="fas fa-location-dot"></i> {{ $portfolio->location }}@endif @if($portfolio->project_date)<span class="me-2"><i class="fas fa-calendar"></i> {{ \Illuminate\Support\Carbon::parse($portfolio->project_date)->format('Y-m-d') }}</span>@endif</div>
                <div class="d-flex gap-2"><a href="{{ route('herafy.craftman.portfolios.edit', $portfolio) }}" class="btn btn-outline-warning flex-grow-1"><i class="fas fa-pen"></i> تعديل</a><form method="POST" action="{{ route('herafy.craftman.portfolios.destroy', $portfolio) }}" data-crud-delete data-confirm-message="هل أنت متأكد من حذف هذا العمل؟">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i><span class="visually-hidden">حذف</span></button></form></div></div>
            </article></div>
        @empty
            <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-images fa-3x text-warning mb-3"></i><h2 class="h5 fw-bold">معرضك فارغ حاليًا</h2><p class="text-muted">أضف أول عمل ليظهر في ملفك أمام الزبائن.</p><a href="{{ route('herafy.craftman.portfolios.create') }}" class="btn btn-warning">إضافة أول عمل</a></div></div></div>
        @endforelse
    </div>
    @if($portfolios->hasPages())<div class="mt-4">{{ $portfolios->links() }}</div>@endif
</div></main>
@endsection
