<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حرفي - @yield('title')</title>

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome 6 (للأيقونات) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ملف CSS الخاص بنا -->
    <link rel="stylesheet" href="{{asset('herafy/css/style.css')}}">
    @yield('style')
</head>

<body>

    <!-- ============================================ -->
    <!-- 1. NAVBAR -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- اللوجو -->
            <a class="navbar-brand fw-bold fs-3" href="{{ route('index') }}">
                <i class="fas fa-hammer text-warning"></i> حرفي
            </a>

            <!-- زر القائمة للجوال -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- روابط النافبار -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('index') }}">الرئيسية</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('showCraftsmen') }}">الحرفيون</a></li>

                    @yield('nav-links')
                    
                    </ul>

                <!-- أزرار تسجيل الدخول / الاشتراك -->
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-warning btn-sm">دخول</a>
                    <a href="{{ route('register') }}" class="btn btn-warning btn-sm">انضم الآن</a>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')



    <!-- ============================================ -->
    <!-- 10. FOOTER (التذييل) -->
    <!-- ============================================ -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold"><i class="fas fa-hammer text-warning"></i> حرفي</h5>
                    <p class="text-white small">منصة تربط المستخدمين بأفضل الحرفيين في المنطقة.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold text-warning">روابط سريعة</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white text-decoration-none">الرئيسية</a></li>
                        <li><a href="#about" class="text-white text-decoration-none">من نحن</a></li>
                        <li><a href="#craftsmen" class="text-white text-decoration-none">الحرفيون</a></li>
                        <li><a href="#testimonials" class="text-white text-decoration-none">الآراء</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">للحرفيين</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white text-decoration-none">كيف تصبح حرفي</a></li>
                        <li><a href="#" class="text-white text-decoration-none">شروط التسجيل</a></li>
                        <li><a href="#" class="text-white text-decoration-none">نصائح للنجاح</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-warning">تواصل معنا</h6>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-phone text-warning"></i> 012 345 6789</li>
                        <li><i class="fas fa-envelope text-warning"></i> info@harfi.com</li>
                        <li><i class="fas fa-map-marker-alt text-warning"></i> فلسطين</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <p class="text-center text-white small mb-0">
                &copy; 2026 حرفي. جميع الحقوق محفوظة
            </p>
        </div>
    </footer>

    <!-- ============================================ -->
    <!-- Bootstrap JS -->
    <!-- ============================================ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ============================================ -->
    <!-- ملف JS الخاص بنا -->
    <!-- ============================================ -->
    <script src="{{asset('herafy/js/script.js')}}"></script>
    @yield('script')
</body>

</html>