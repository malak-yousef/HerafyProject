@extends('herafy.parent')

@section('title', 'الملف الشخصي للحرفي')

@section('style')
<style>
    .craftman-profile-page { background: #f8f9fa; min-height: 65vh; }
    .profile-hero, .profile-section { border: 0; border-radius: 20px; }
    .craftman-avatar { width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 4px solid #fff3cd; }
    .craftman-avatar-fallback { width: 130px; height: 130px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #fff3cd; color: #8a6200; font-size: 3rem; }
    .profile-label { color: #6c757d; font-size: .85rem; }
    .rating-stars { color: #ffc107; }
</style>
@endsection

@section('content')
@php
    $user = $craftman->user;
    $photoUrl = $user->profilePhotoUrl();
    $verificationLabels = ['pending' => 'قيد المراجعة', 'approved' => 'موثق', 'rejected' => 'مرفوض'];
@endphp
<main class="craftman-profile-page py-5">
    <div class="container">
        <section class="card profile-hero shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-auto text-center">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="صورة {{ $user->name }}" class="craftman-avatar" width="130" height="130">
                        @else
                            <span class="craftman-avatar-fallback"><i class="fas fa-user"></i></span>
                        @endif
                    </div>
                    <div class="col text-center text-md-start">
                        <div class="float-md-end mb-3">
                            <a href="{{ route('herafy.craftman.portfolios.index') }}" class="btn btn-outline-warning">
                                <i class="fas fa-images"></i> معرض أعمالي
                            </a>
                            <a href="{{ route('herafy.craftman.profile.edit') }}" class="btn btn-warning">
                                <i class="fas fa-user-pen"></i> تعديل الملف الشخصي
                            </a>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mb-2">
                            <span class="badge text-bg-warning">{{ $craftman->profession?->name ?? 'بدون تخصص' }}</span>
                            @if($verification)<span class="badge {{ \App\Support\StatusLabel::solidBadgeClass($verification->status) }}">{{ \App\Support\StatusLabel::label($verification->status) }}</span>@endif
                        </div>
                        <h1 class="h2 fw-bold mb-1">{{ $user->name }}</h1>
                        <div class="rating-stars mb-2"><i class="fas fa-star"></i> <strong>{{ number_format($averageRating, 1) }}</strong> <span class="text-muted">({{ $reviewsCount }} تقييم)</span></div>
                        @if($craftman->description)<p class="text-muted mb-0">{{ $craftman->description }}</p>@endif
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="card profile-section shadow-sm h-100"><div class="card-body p-4"><h2 class="h5 fw-bold mb-4">المعلومات الشخصية والمهنية</h2><div class="row g-4">
                    <div class="col-md-6"><div class="profile-label">اسم المستخدم</div><strong>{{ $user->user_name }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">البريد الإلكتروني</div><strong>{{ $user->email }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">رقم الهاتف</div><strong>{{ $user->mobile }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">العنوان</div><strong>{{ $user->address }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">الجنس</div><strong>{{ $user->gender === 'male' ? 'ذكر' : 'أنثى' }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">سنوات الخبرة</div><strong>{{ $craftman->experience_years }} سنة</strong></div>
                    <div class="col-md-6"><div class="profile-label">السعر بالساعة</div><strong><x-money :value="$craftman->hourly_rate" /> / ساعة</strong></div>
                    <div class="col-md-6"><div class="profile-label">السعر باليوم</div><strong><x-money :value="$craftman->daily_rate" /> / يوم</strong></div>
                </div></div></section>
            </div>
            <div class="col-lg-5">
                <section class="card profile-section shadow-sm h-100"><div class="card-body p-4"><h2 class="h5 fw-bold mb-4">أحدث التقييمات</h2>
                    @forelse($recentReviews as $review)
                        <article class="py-3 border-top"><div class="d-flex justify-content-between"><span class="rating-stars">@for($star = 1; $star <= 5; $star++)<i class="{{ $star <= $review->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</span><small class="text-muted">{{ $review->created_at->format('Y-m-d') }}</small></div>@if($review->comment)<p class="mt-2 mb-0 text-muted">{{ $review->comment }}</p>@endif</article>
                    @empty
                        <div class="text-center text-muted py-4"><i class="far fa-star fa-2x text-warning mb-2"></i><p class="mb-0">لا توجد تقييمات معتمدة بعد.</p></div>
                    @endforelse
                </div></section>
            </div>
        </div>
    </div>
</main>
@endsection
