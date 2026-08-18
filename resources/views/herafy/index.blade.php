@extends('herafy.parent')

@section('title' , 'منصة الحرفيين')

@section('style')

@endsection

@section('nav-links')
<li class="nav-item"><a class="nav-link" href="#about">من نحن</a></li>
<li class="nav-item"><a class="nav-link" href="#testimonials">الآراء</a></li>
<li class="nav-item"><a class="nav-link" href="#gallery">معرض الأعمال</a></li>

@endsection

@section('content')

<!-- ============================================ -->
<!-- 2. HERO SECTION -->
<!-- ============================================ -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="display-3 fw-bold">
                    احترف مع <span class="text-warning">حرفي</span>
                </h1>
                <p class="lead text-muted mt-3">
                    منصة تربطك بأفضل الحرفيين في مجال النجارة، السباكة، البلاط، الكهرباء، وأكثر...
                </p>
                <p class="text-muted">
                    قدم طلبك واستقبل عروضاً من حرفيين محترفين، واختر الأنسب لك!
                </p>
                <div class="mt-4 d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="{{ route('request') }}" class="btn btn-warning btn-lg px-5">
                        <i class="fas fa-pen"></i> قدم طلبك
                    </a>
                    <a href="#craftsmen" class="btn btn-outline-secondary btn-lg px-5">
                        <i class="fas fa-search"></i> استعرض الحرفيين
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0 text-center">
                <img src="{{ asset('herafy/img/hero.png') }}" alt="حرفي"
                    class="img-fluid rounded-4 ">
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 3. ABOUT US (من نحن) -->
<!-- ============================================ -->
<section id="about" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <h2 class="fw-bold">من نحن</h2>
                <p class="text-muted">
                    منصة <strong>حرفي</strong> هي جسر التواصل بين المستخدمين والحرفيين المحترفين.
                </p>
                <ul class="list-unstyled">
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
                <img src="{{ asset('herafy/img/hero1.png') }}" alt="من نحن"
                    class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 4. CRAFTSMEN (الحرفيون) -->
<!-- ============================================ -->
<section id="craftsmen" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">أبرز الحرفيين</h2>
            <p class="text-muted">تعرف على نخبة من الحرفيين المحترفين في مجالات متنوعة</p>
        </div>

        <div class="row g-4">
            <!-- بطاقة حرفي 1 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="https://via.placeholder.com/300x200/FFC107/FFFFFF?text=نجار" class="card-img-top"
                        alt="نجار">
                    <div class="card-body text-center">
                        <h5 class="card-title">أحمد النجار</h5>
                        <span class="badge bg-warning text-dark">نجارة</span>
                        <p class="card-text small text-muted mt-2">
                            <i class="fas fa-star text-warning"></i> 4.8 (120 تقييم)
                        </p>
                        <a href="craftsman-profile.html" class="btn btn-outline-warning btn-sm">عرض الملف</a>
                    </div>
                </div>
            </div>

            <!-- بطاقة حرفي 2 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="https://via.placeholder.com/300x200/FFC107/FFFFFF?text=سباك" class="card-img-top"
                        alt="سباك">
                    <div class="card-body text-center">
                        <h5 class="card-title">محمد السباك</h5>
                        <span class="badge bg-warning text-dark">سباكة</span>
                        <p class="card-text small text-muted mt-2">
                            <i class="fas fa-star text-warning"></i> 4.9 (85 تقييم)
                        </p>
                        <a href="craftsman-profile.html" class="btn btn-outline-warning btn-sm">عرض الملف</a>
                    </div>
                </div>
            </div>

            <!-- بطاقة حرفي 3 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="https://via.placeholder.com/300x200/FFC107/FFFFFF?text=بلاط" class="card-img-top"
                        alt="بلاط">
                    <div class="card-body text-center">
                        <h5 class="card-title">خالد المبلط</h5>
                        <span class="badge bg-warning text-dark">تبليط</span>
                        <p class="card-text small text-muted mt-2">
                            <i class="fas fa-star text-warning"></i> 4.7 (64 تقييم)
                        </p>
                        <a href="craftsman-profile.html" class="btn btn-outline-warning btn-sm">عرض الملف</a>
                    </div>
                </div>
            </div>

            <!-- بطاقة حرفي 4 -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="https://via.placeholder.com/300x200/FFC107/FFFFFF?text=كهربائي" class="card-img-top"
                        alt="كهربائي">
                    <div class="card-body text-center">
                        <h5 class="card-title">سامي الكهربائي</h5>
                        <span class="badge bg-warning text-dark">كهرباء</span>
                        <p class="card-text small text-muted mt-2">
                            <i class="fas fa-star text-warning"></i> 4.6 (52 تقييم)
                        </p>
                        <a href="craftsman-profile.html" class="btn btn-outline-warning btn-sm">عرض الملف</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('showCraftsmen') }}" class="btn btn-warning">عرض جميع الحرفيين <i class="fas fa-arrow-left"></i></a>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 5. TESTIMONIALS (الآراء) -->
<!-- ============================================ -->
<section id="testimonials" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">آراء عملائنا</h2>
            <p class="text-muted">ماذا يقول عنا المستخدمون والحرفيون</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="card-text">
                            "منصة رائعة! وجدت نجار ممتاز خلال ساعة من تقديم الطلب. أنجز العمل بإتقان."
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50/FFC107/FFFFFF?text=U"
                                class="rounded-circle me-2" width="50" alt="مستخدم">
                            <div>
                                <h6 class="mb-0">سارة أحمد</h6>
                                <small class="text-muted">مستخدم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="card-text">
                            "كحرفي، المنصة ساعدتني أوصل لزبائن جدد بشكل مستمر. أنصح أي حرفي بالتسجيل."
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50/FFC107/FFFFFF?text=C"
                                class="rounded-circle me-2" width="50" alt="حرفي">
                            <div>
                                <h6 class="mb-0">أحمد النجار</h6>
                                <small class="text-muted">حرفي</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="card-text">
                            "سهولة الاستخدام وسرعة الاستجابة. أفضل منصة للخدمات المنزلية في منطقتنا."
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://via.placeholder.com/50/FFC107/FFFFFF?text=U"
                                class="rounded-circle me-2" width="50" alt="مستخدم">
                            <div>
                                <h6 class="mb-0">فاطمة خالد</h6>
                                <small class="text-muted">مستخدم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 6. GALLERY (معرض الأعمال) -->
<!-- ============================================ -->
<section id="gallery" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">معرض الأعمال</h2>
            <p class="text-muted">أحدث المشاريع التي نفذها حرفيونا</p>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=مطبخ"
                    class="img-fluid rounded-3 w-100" alt="مطبخ">
            </div>
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=حمام"
                    class="img-fluid rounded-3 w-100" alt="حمام">
            </div>
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=غرفة"
                    class="img-fluid rounded-3 w-100" alt="غرفة">
            </div>
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=سباكة"
                    class="img-fluid rounded-3 w-100" alt="سباكة">
            </div>
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=كهرباء"
                    class="img-fluid rounded-3 w-100" alt="كهرباء">
            </div>
            <div class="col-md-4">
                <img src="https://via.placeholder.com/400x300/FFC107/FFFFFF?text=حديقة"
                    class="img-fluid rounded-3 w-100" alt="حديقة">
            </div>
        </div>
    </div>
</section>



<!-- ============================================ -->
<!-- 7. STATISTICS (إحصائيات) -->
<!-- ============================================ -->
<section class="py-5 bg-warning text-dark">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3 col-6">
                <h2 class="display-4 fw-bold counter" data-target="150">0</h2>
                <p class="fw-bold">حرفي مسجل</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="display-4 fw-bold counter" data-target="500">0</h2>
                <p class="fw-bold">طلب منفذ</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="display-4 fw-bold counter" data-target="98">0</h2>
                <p class="fw-bold">% نسبة رضا</p>
            </div>
            <div class="col-md-3 col-6">
                <h2 class="display-4 fw-bold counter" data-target="4.8">0</h2>
                <p class="fw-bold">متوسط التقييم</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 8. HOW IT WORKS (كيف تعمل المنصة) -->
<!-- ============================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">كيف تعمل المنصة؟</h2>
            <p class="text-muted">4 خطوات بسيطة للحصول على خدمتك</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-pen"></i>
                </div>
                <h5 class="mt-3">1. قدم طلبك</h5>
                <p class="text-muted small">حدد نوع الحرفة ووصف العمل المطلوب</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-handshake"></i>
                </div>
                <h5 class="mt-3">2. استقبل العروض</h5>
                <p class="text-muted small">حرفيون مقيّمون يقدمون عروضهم</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="step-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center"
                    style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="mt-3">3. اختر الأنسب</h5>
                <p class="text-muted small">قارن العروض واختر الحرفي المناسب</p>
            </div>
            <div class="col-md-3 text-center">
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
<section id="faq" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">الأسئلة الشائعة</h2>
            <p class="text-muted">أجوبة لأكثر الأسئلة تكراراً</p>
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