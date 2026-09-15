@extends('herafy.parent')

@section('title', 'عرض الملف الشخصي')

@section('style')
<style>
    .profile-page { background: #f8f9fa; min-height: 65vh; }
    .profile-card, .profile-details { border: 0; border-radius: 20px; }
    .profile-avatar { width: 130px; height: 130px; object-fit: cover; border-radius: 50%; border: 4px solid #fff3cd; }
    .profile-avatar-fallback { width: 130px; height: 130px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: #fff3cd; color: #8a6200; font-size: 3rem; }
    .profile-label { color: #6c757d; font-size: .85rem; margin-bottom: .25rem; }
</style>
@endsection

@section('content')
@php
    $user = $customer->user;
    $photoUrl = $user->profilePhotoUrl();
    $genderLabel = $user->gender === 'female' ? 'أنثى' : 'ذكر';
@endphp
<main class="profile-page py-5">
    <div class="container">
        <section class="card profile-card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-auto text-center">
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="صورة {{ $user->name }}" class="profile-avatar">
                        @else
                            <span class="profile-avatar-fallback"><i class="fas fa-user"></i></span>
                        @endif
                    </div>
                    <div class="col text-center text-md-start">
                        <div class="float-md-end mb-3">
                            <a href="{{ route('herafy.customer.profile.edit') }}" class="btn btn-warning"><i class="fas fa-user-pen ms-1"></i> تعديل الملف الشخصي</a>
                        </div>
                        <h1 class="h3 fw-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-muted mb-0">&#64;{{ $user->user_name }}</p>
                        <p class="mt-2 mb-0"><i class="fas fa-star text-warning"></i> <strong>{{ number_format($receivedReviewsAverage, 1) }}</strong> <span class="text-muted">({{ $receivedReviewsCount }} تقييم من الحرفيين)</span></p>
                    </div>
                </div>
            </div>
        </section>
        <section class="card profile-details shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h4 fw-bold mb-4">بيانات الحساب</h2>
                <div class="row g-4">
                    <div class="col-md-6"><div class="profile-label">الاسم</div><strong>{{ $user->name }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">اسم المستخدم</div><strong>{{ $user->user_name }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">البريد الإلكتروني</div><strong>{{ $user->email }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">رقم الهاتف</div><strong>{{ $user->mobile }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">العنوان</div><strong>{{ $user->address }}</strong></div>
                    <div class="col-md-6"><div class="profile-label">الجنس</div><strong>{{ $genderLabel }}</strong></div>
                </div>
            </div>
        </section>
        <section class="card profile-details shadow-sm mt-4">
            <div class="card-body p-4 p-lg-5">
                <h2 class="h4 fw-bold mb-4">تقييمات الحرفيين</h2>
                <div class="row g-3">
                    @forelse($receivedReviews as $review)
                        <div class="col-md-6">
                            <article class="border rounded-3 p-3 h-100">
                                <div class="d-flex justify-content-between gap-2">
                                    <span class="text-warning">@for($star = 1; $star <= 5; $star++)<i class="{{ $star <= $review->rating ? 'fas' : 'far' }} fa-star"></i>@endfor</span>
                                    <small class="text-muted">{{ $review->created_at->format('Y-m-d') }}</small>
                                </div>
                                @if($review->comment)<p class="text-muted mt-2 mb-1">{{ $review->comment }}</p>@endif
                                <small>بواسطة {{ $review->craftman?->user?->name ?? 'حرفي' }}</small>
                            </article>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted py-3">لا توجد تقييمات من الحرفيين بعد.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
