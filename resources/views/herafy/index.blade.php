@extends('herafy.parent')

@section('title' , 'منصة الحرفيين')

@section('style')

@endsection

@section('content')

<!-- ============================================ -->
<!-- 2. HERO SECTION -->
<!-- ============================================ -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5 hero-row">
            <div class="col-lg-6 text-center text-lg-start">
                <div class="hero-content">
                <span class="hero-kicker"><i class="fas fa-shield-halved"></i> خدمات موثوقة وحرفيون محترفون</span>
                <h1 class="fw-bold">
                    احترف مع <span class="text-warning">حرفي</span>
                </h1>
                <p class="hero-description">
                    منصة تربطك بأفضل الحرفيين في مجال النجارة، السباكة، البلاط، الكهرباء، وأكثر...
                </p>
                <p class="hero-subtitle">
                    قدم طلبك واستقبل عروضاً من حرفيين محترفين، واختر الأنسب لك!
                </p>
                <div class="hero-actions d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('request') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-pen"></i> قدم طلبك
                    </a>
                    <a href="#craftsmen" class="btn btn-outline-dark btn-lg">
                        <i class="fas fa-search"></i> استعرض الحرفيين
                    </a>
                </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image-wrap">
                <img src="{{ asset('herafy/img/Codex Image 26 Aug 2026, 12_48_14.webp') }}" alt="حرفي"
                    class="hero-image" width="1086" height="1448">
                    <span class="hero-image-badge"><i class="fas fa-star"></i> جودة تستحق الثقة</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 3. ABOUT US (من نحن) -->
<!-- ============================================ -->
<section id="about" class="home-section about-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2 about-content">
                <span class="section-kicker">عن المنصة</span>
                <h2 class="section-title">من نحن</h2>
                <p class="section-description">
                    منصة <strong>حرفي</strong> هي جسر التواصل بين المستخدمين والحرفيين المحترفين.
                </p>
                <ul class="list-unstyled about-list">
                    <li class="mb-2"><i class="fas fa-check-circle text-warning"></i> نوفر لك حرفيين موثوقين
                        ومقيّمين</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-warning"></i> تقدم طلبك بسهولة وتستقبل
                        عروضاً تنافسية</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-warning"></i> تقييمات حقيقية من عملاء سابقين
                    </li>
                    <li class="mb-2"><i class="fas fa-check-circle text-warning"></i> منصة آمنة وموثوقة للجميع</li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1 text-center">
                <div class="about-image-wrap">
                    <img src="{{ asset('herafy/img/hero1.webp') }}" alt="مجموعة من حرفيي منصة حرفي" class="about-image" width="1408" height="768" loading="lazy" decoding="async">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 4. CRAFTSMEN (الحرفيون) -->
<!-- ============================================ -->
<section id="craftsmen" class="home-section featured-section">
    <div class="container">
        <div class="section-heading text-center">
            <span class="section-kicker">مختارون بعناية</span>
            <h2 class="section-title">أبرز الحرفيين</h2>
            <p class="section-description">تعرف على نخبة من الحرفيين المحترفين في مجالات متنوعة</p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($featuredCraftmen as $craftman)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 craftsman-card hover-card">
                        @if($craftman->user?->profilePhotoUrl())
                            <img src="{{ $craftman->user->profilePhotoUrl() }}" class="card-img-top" alt="صورة {{ $craftman->user->name }}" style="height: 230px; object-fit: cover;" width="230" height="230" loading="lazy" decoding="async">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-secondary" style="height: 230px;" role="img" aria-label="لا توجد صورة">
                                <i class="fas fa-user fa-5x" aria-hidden="true"></i>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $craftman->user->name }}</h5>
                            <span class="badge bg-warning text-dark">{{ $craftman->profession?->name ?? 'حرفي' }}</span>
                            <p class="card-text small text-muted mt-2">
                                <i class="fas fa-star text-warning" aria-hidden="true"></i>
                                {{ number_format((float) ($craftman->approved_reviews_avg_rating ?? 0), 1) }}
                                ({{ $craftman->approved_reviews_count }} تقييم)
                            </p>
                            <a href="{{ route('herafy.craftmen.show', $craftman) }}" class="btn btn-outline-warning craftsman-profile-btn">عرض الملف</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    لم يتم اختيار حرفيين مميزين بعد.
                </div>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('showCraftsmen') }}" class="btn btn-warning">عرض جميع الحرفيين <i class="fas fa-arrow-left"></i></a>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 5. TESTIMONIALS (الآراء) -->
<!-- ============================================ -->
<section id="testimonials" class="home-section testimonials-section">
    <div class="container">
        <div class="section-heading text-center">
            <span class="section-kicker">تجارب حقيقية</span>
            <h2 class="section-title">آراء عملائنا</h2>
            <p class="section-description">ماذا يقول عنا المستخدمون والحرفيون</p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($testimonials as $review)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 testimonial-card">
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2" aria-label="التقييم {{ $review->rating }} من 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <i class="fas fa-star {{ $star <= $review->rating ? 'text-warning' : 'text-secondary opacity-25' }}" aria-hidden="true"></i>
                                @endfor
                            </div>
                            <p class="card-text flex-grow-1">“{{ $review->comment }}”</p>
                            <div class="d-flex align-items-center gap-2 mt-3">
                                @if($review->customer?->user?->profilePhotoUrl())
                                    <img src="{{ $review->customer->user->profilePhotoUrl() }}" class="rounded-circle" width="50" height="50" style="object-fit: cover;" alt="صورة {{ $review->customer->user->name }}" loading="lazy" decoding="async">
                                @else
                                    <span class="rounded-circle bg-warning-subtle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" aria-hidden="true"><i class="fas fa-user text-warning"></i></span>
                                @endif
                                <div>
                                    <h3 class="h6 mb-0">{{ $review->customer->user->name }}</h3>
                                    <small class="text-muted">عميل قيّم {{ $review->craftsman?->user?->name ?? 'الخدمة' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">لا توجد آراء معتمدة للعرض بعد.</div>
            @endforelse
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 6. GALLERY (معرض الأعمال) -->
<!-- ============================================ -->
<section id="gallery" class="home-section gallery-section">
    <div class="container">
        <div class="section-heading text-center">
            <span class="section-kicker">أعمال مميزة</span>
            <h2 class="section-title">معرض الأعمال</h2>
            <p class="section-description">أحدث المشاريع التي نفذها حرفيونا</p>
        </div>

        <div class="row g-3">
            @forelse($featuredPortfolios as $portfolio)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('herafy.craftmen.portfolios.show', [$portfolio->craftman, $portfolio]) }}" class="card h-100 text-decoration-none text-dark overflow-hidden portfolio-card">
                        @if($portfolio->portfolioCover_image)
                            <img src="{{ asset('storage/images/portfolio/'.$portfolio->portfolioCover_image) }}" class="card-img-top" style="height: 260px; object-fit: cover;" alt="{{ $portfolio->title }}" width="260" height="260" loading="lazy" decoding="async">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-secondary" style="height: 260px;" role="img" aria-label="لا توجد صورة للعمل">
                                <i class="fas fa-images fa-4x" aria-hidden="true"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-2">{{ $portfolio->title }}</h3>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-user text-warning" aria-hidden="true"></i>
                                {{ $portfolio->craftman?->user?->name }}
                                @if($portfolio->craftman?->profession)
                                    · {{ $portfolio->craftman->profession->name }}
                                @endif
                            </p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">لم يتم اختيار أعمال مميزة بعد.</div>
            @endforelse
        </div>
    </div>
</section>



<!-- ============================================ -->
<!-- 7. STATISTICS (إحصائيات) -->
<!-- ============================================ -->
<section class="home-section stats-section text-dark">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6 stat-item">
                <h2 class="display-4 fw-bold counter" data-target="{{ $homepageStats['active_craftmen'] }}">0</h2>
                <p class="fw-bold">حرفي نشط</p>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <h2 class="display-4 fw-bold counter" data-target="{{ $homepageStats['completed_jobs'] }}">0</h2>
                <p class="fw-bold">طلب منفذ</p>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <h2 class="display-4 fw-bold counter" data-target="{{ $homepageStats['satisfaction_rate'] }}">0</h2>
                <p class="fw-bold">% نسبة رضا</p>
            </div>
            <div class="col-md-3 col-6 stat-item">
                <h2 class="display-4 fw-bold counter" data-target="{{ $homepageStats['average_rating'] }}">0</h2>
                <p class="fw-bold">متوسط التقييم</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 8. HOW IT WORKS (كيف تعمل المنصة) -->
<!-- ============================================ -->
<section class="home-section process-section">
    <div class="container">
        <div class="section-heading text-center">
            <span class="section-kicker">بكل سهولة</span>
            <h2 class="section-title">كيف تعمل المنصة؟</h2>
            <p class="section-description">4 خطوات بسيطة للحصول على خدمتك</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 text-center process-step">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-pen"></i>
                </div>
                <h5 class="mt-3">1. قدم طلبك</h5>
                <p class="text-muted small">حدد نوع الحرفة ووصف العمل المطلوب</p>
            </div>
            <div class="col-md-3 text-center process-step">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-handshake"></i>
                </div>
                <h5 class="mt-3">2. استقبل العروض</h5>
                <p class="text-muted small">حرفيون مقيّمون يقدمون عروضهم</p>
            </div>
            <div class="col-md-3 text-center process-step">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="mt-3">3. اختر الأنسب</h5>
                <p class="text-muted small">قارن العروض واختر الحرفي المناسب</p>
            </div>
            <div class="col-md-3 text-center process-step">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-star"></i>
                </div>
                <h5 class="mt-3">4. قيّم الخدمة</h5>
                <p class="text-muted small">بعد الانتهاء، شارك تجربتك</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 9. FAQ (الأسئلة الشائعة) -->
<!-- ============================================ -->
<section id="faq" class="home-section faq-section">
    <div class="container">
        <div class="section-heading text-center">
            <span class="section-kicker">نجيبك بوضوح</span>
            <h2 class="section-title">الأسئلة الشائعة</h2>
            <p class="section-description">أجوبة لأكثر الأسئلة تكراراً</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- سؤال 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1">
                                كيف أضمن جودة العمل؟
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                جميع الحرفيين مسجلين بتقييمات حقيقية من عملاء سابقين. يمكنك مشاهدة تقييماتهم ومعرض
                                أعمالهم قبل الاختيار.
                            </div>
                        </div>
                    </div>
                    <!-- سؤال 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                هل الخدمة مجانية للحرفي؟
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                نعم، التسجيل مجاني للحرفيين. يتم خصم نسبة بسيطة عند إتمام الطلب فقط.
                            </div>
                        </div>
                    </div>
                    <!-- سؤال 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                كيف يتم الدفع؟
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يمكن الدفع نقداً بعد إتمام العمل أو عبر البطاقة البنكية من خلال المنصة (خدمة
                                قريباً).
                            </div>
                        </div>
                    </div>
                    <!-- سؤال 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                ماذا لو لم يعجبني العمل؟
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                يمكنك التواصل مع فريق الدعم، وسنعمل على حل المشكلة بينك وبين الحرفي بأفضل طريقة.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')

@endsection
