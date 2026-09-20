@extends('herafy.parent')
@section('title', $craftman->user->name)
@section('style')
<style>
    .public-profile-page{background:#f8f9fa;min-height:70vh}.public-profile-hero,.public-section,.public-work{border:0;border-radius:20px}.public-avatar{width:130px;height:130px;border-radius:50%;object-fit:cover;border:5px solid #fff3cd}.public-avatar-fallback{width:130px;height:130px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#fff3cd;color:#8a6200;font-size:3rem}.public-work{overflow:hidden;transition:.2s}.public-work:hover{transform:translateY(-3px);box-shadow:0 12px 28px rgba(0,0,0,.09)!important}.public-work-cover{width:100%;height:220px;object-fit:cover}.rating-stars{color:#ffc107}
</style>
@endsection
@section('content')
@php($photoUrl = $craftman->user->profilePhotoUrl())
<main class="public-profile-page py-5"><div class="container">
    <section class="card public-profile-hero shadow-sm mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4">
        <div class="col-md-auto text-center">@if($photoUrl)<img src="{{ $photoUrl }}" alt="صورة {{ $craftman->user->name }}" class="public-avatar" width="130" height="130">@else<span class="public-avatar-fallback"><i class="fas fa-user"></i></span>@endif</div>
        <div class="col text-center text-md-start"><span class="badge text-bg-warning mb-2">{{ $craftman->profession?->name ?? 'بدون تخصص' }}</span><h1 class="h2 fw-bold">{{ $craftman->user->name }}</h1><div class="rating-stars mb-2"><i class="fas fa-star"></i> <strong>{{ number_format((float) ($craftman->approved_reviews_avg_rating ?? 0), 1) }}</strong> <span class="text-muted">({{ $craftman->approved_reviews_count }} تقييم)</span></div>@if($craftman->description)<p class="text-muted mb-3">{{ $craftman->description }}</p>@endif<div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 small"><span><i class="fas fa-award text-warning"></i> {{ $craftman->experience_years }} سنة خبرة</span><span><i class="fas fa-clock text-warning"></i> {{ number_format((float)$craftman->hourly_rate,2) }} ₪/ساعة</span><span><i class="fas fa-calendar-day text-warning"></i> {{ number_format((float)$craftman->daily_rate,2) }} ₪/يوم</span></div></div>
        @unless(auth('craftman')->check())<div class="col-md-auto"><a href="{{ route('request', ['profession_id' => $craftman->profession_id]) }}" class="btn btn-warning btn-lg fw-bold"><i class="fas fa-paper-plane ms-1"></i> اطلب خدمة</a></div>@endunless
    </div></div></section>

    <section class="mb-5"><div class="d-flex justify-content-between align-items-end mb-3"><div><h2 class="h4 fw-bold mb-1">معرض الأعمال</h2><p class="text-muted mb-0">نماذج من المشاريع السابقة.</p></div><span class="badge text-bg-light">{{ $portfolios->total() }} عمل</span></div>
        <div class="row g-4">@forelse($portfolios as $portfolio)<div class="col-md-6 col-xl-4"><article class="card public-work shadow-sm h-100"><img src="{{ asset('storage/images/portfolio/'.$portfolio->portfolioCover_image) }}" alt="{{ $portfolio->title }}" class="public-work-cover" width="300" height="220" loading="lazy" decoding="async"><div class="card-body p-4"><h3 class="h5 fw-bold">{{ $portfolio->title }}</h3><p class="text-muted">{{ Str::limit($portfolio->description,110) }}</p><a href="{{ route('herafy.craftmen.portfolios.show', [$craftman, $portfolio]) }}" class="btn btn-outline-warning w-100">عرض تفاصيل العمل</a></div></article></div>@empty<div class="col-12"><div class="card public-section shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-images fa-3x text-warning mb-3"></i><p class="text-muted mb-0">لم يضف الحرفي أعمالًا للعرض بعد.</p></div></div></div>@endforelse</div>
        @if($portfolios->hasPages())<div class="mt-4">{{ $portfolios->links() }}</div>@endif
    </section>

    <section class="card public-section shadow-sm"><div class="card-body p-4"><h2 class="h4 fw-bold mb-3">أحدث التقييمات</h2><div class="row g-3">@forelse($reviews as $review)<div class="col-md-6"><article class="border rounded-3 p-3 h-100"><div class="d-flex justify-content-between"><span class="rating-stars">@for($star=1;$star<=5;$star++)<i class="{{ $star <= $review->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</span><small class="text-muted">{{ $review->created_at->format('Y-m-d') }}</small></div>@if($review->comment)<p class="text-muted mt-2 mb-0">{{ $review->comment }}</p>@endif</article></div>@empty<div class="col-12 text-center text-muted py-4">لا توجد تقييمات معتمدة بعد.</div>@endforelse</div></div></section>
</div></main>
@endsection
