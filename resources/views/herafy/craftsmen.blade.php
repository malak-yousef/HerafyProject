@extends('herafy.parent')

@section('title', 'استعرض الحرفيين')

@section('style')
<style>
    .craftmen-page { background: linear-gradient(135deg, #f8f9fa, #fff); min-height: 75vh; }
    .craftman-card { border: 0; border-radius: 20px; transition: transform .2s ease, box-shadow .2s ease; }
    .craftman-card:hover { transform: translateY(-4px); box-shadow: 0 14px 32px rgba(0, 0, 0, .1) !important; }
    .craftman-avatar { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 4px solid #fff3cd; }
    .craftman-avatar-fallback { width: 88px; height: 88px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #fff3cd; color: #8a6200; font-size: 2rem; }
    .filter-card { border: 0; border-radius: 20px; }
</style>
@endsection

@section('content')
<main class="craftmen-page py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="h2 fw-bold">استعرض الحرفيين</h1>
            <p class="text-muted mb-0">ابحث في ملفات الحرفيين المسجلين واختر الخبرة المناسبة.</p>
        </div>

        <form method="GET" action="{{ route('showCraftsmen') }}" class="card filter-card shadow-sm mb-5">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label for="search" class="form-label fw-semibold">ابحث عن حرفي</label>
                        <input id="search" name="search" type="search" maxlength="100" class="form-control" value="{{ $filters['search'] }}" placeholder="الاسم أو اسم المستخدم">
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label for="profession_id" class="form-label fw-semibold">المهنة</label>
                        <select id="profession_id" name="profession_id" class="form-select">
                            <option value="">كل المهن</option>
                            @foreach($professions as $profession)
                                <option value="{{ $profession->id }}" @selected((int) $filters['profession_id'] === $profession->id)>{{ $profession->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label for="sort" class="form-label fw-semibold">الترتيب</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="newest" @selected($filters['sort'] === 'newest')>الأحدث</option>
                            <option value="name_asc" @selected($filters['sort'] === 'name_asc')>الاسم أ-ي</option>
                            <option value="name_desc" @selected($filters['sort'] === 'name_desc')>الاسم ي-أ</option>
                            <option value="experience_desc" @selected($filters['sort'] === 'experience_desc')>الأكثر خبرة</option>
                            <option value="experience_asc" @selected($filters['sort'] === 'experience_asc')>الأقل خبرة</option>
                        </select>
                    </div>
                    <div class="col-lg-2 d-grid gap-2">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-search"></i> بحث</button>
                        <a href="{{ route('showCraftsmen') }}" class="btn btn-outline-secondary btn-sm">مسح الفلاتر</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="row g-4">
            @forelse($craftmen as $craftman)
                @php($photoUrl = $craftman->user->profilePhotoUrl())
                <div class="col-md-6 col-xl-4" data-testid="craftman-card" data-craftman-id="{{ $craftman->id }}">
                    <article class="card craftman-card h-100 shadow-sm">
                        <div class="card-body p-4 text-center">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="صورة {{ $craftman->user->name }}" class="craftman-avatar mb-3" width="88" height="88" loading="lazy" decoding="async">
                            @else
                                <span class="craftman-avatar-fallback mb-3"><i class="fas fa-user"></i></span>
                            @endif
                            <h2 class="h5 fw-bold mb-1">{{ $craftman->user->name }}</h2>
                            <span class="badge text-bg-warning mb-3">{{ $craftman->profession?->name ?? 'بدون مهنة' }}</span>

                            <div class="small text-muted mb-3">
                                @if($craftman->approved_reviews_count > 0)
                                    <span class="text-warning"><i class="fas fa-star"></i></span>
                                    <strong>{{ number_format((float) $craftman->approved_reviews_avg_rating, 1) }}</strong>
                                    <span>({{ $craftman->approved_reviews_count }} تقييم)</span>
                                @else
                                    <span>لا توجد تقييمات بعد</span>
                                @endif
                            </div>

                            <div class="row g-2 text-start">
                                <div class="col-12"><i class="fas fa-award text-warning ms-1"></i> الخبرة: {{ $craftman->experience_years }} سنة</div>
                                <div class="col-12"><i class="fas fa-clock text-warning ms-1"></i> الساعة: <x-money :value="$craftman->hourly_rate" /></div>
                                <div class="col-12"><i class="fas fa-calendar-day text-warning ms-1"></i> اليوم: <x-money :value="$craftman->daily_rate" /></div>
                            </div>
                            <a href="{{ route('herafy.craftmen.show', $craftman) }}" class="btn btn-outline-warning w-100 mt-4">
                                <i class="fas fa-id-card ms-1"></i> عرض الملف ومعرض الأعمال
                            </a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center bg-white rounded-4 shadow-sm p-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        @if($filters['search'] || $filters['profession_id'])
                            <h2 class="h5">لا يوجد حرفيون مطابقون لخيارات البحث.</h2>
                            <a href="{{ route('showCraftsmen') }}" class="btn btn-outline-warning mt-2">عرض كل الحرفيين</a>
                        @else
                            <h2 class="h5">لا يوجد حرفيون متاحون حالياً.</h2>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        @if($craftmen->hasPages())
            <div class="mt-5">{{ $craftmen->links() }}</div>
        @endif
    </div>
</main>
@endsection
