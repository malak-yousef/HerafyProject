<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حرفي - @yield('title')</title>

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Font Awesome 6 (للأيقونات) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ملف CSS الخاص بنا -->
    <link rel="stylesheet" href="{{asset('herafy/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('herafy/css/navbar.css') }}">
    @yield('style')
</head>

<body>

    <!-- ============================================ -->
    <!-- 1. NAVBAR -->
    <!-- ============================================ -->
    @if(! auth('customer')->check() && ! auth('craftman')->check())
        <x-herafy.guest-navbar />
    @else
    <nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm sticky-top" aria-label="التنقل الرئيسي">
        <div class="container-fluid px-3 px-xl-5">
            <!-- اللوجو -->
            <a class="navbar-brand fw-bold fs-3" href="{{ route('index') }}">
                <i class="fas fa-hammer text-warning"></i> حرفي
            </a>

            <!-- زر القائمة للجوال -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="فتح القائمة">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav align-items-xl-center gap-xl-1 me-xl-3 mb-3 mb-xl-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('index', 'main', 'herafy.craftman.dashboard') ? 'active' : '' }}"
                            href="{{ auth('craftman')->check() ? route('herafy.craftman.dashboard') : route('index') }}">الرئيسية</a>
                    </li>

                    @auth('customer')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('request') ? 'active' : '' }}" href="{{ route('request') }}">
                                <i class="fas fa-plus-circle nav-icon"></i> طلب خدمة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.customer.orders.*') ? 'active' : '' }}"
                                href="{{ route('herafy.customer.orders.index') }}">
                                <i class="fas fa-clipboard-list nav-icon"></i> طلباتي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('showCraftsmen') ? 'active' : '' }}"
                                href="{{ route('showCraftsmen') }}">الحرفيون</a>
                        </li>
                    @elseauth('craftman')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.order.*') ? 'active' : '' }}"
                                href="{{ route('herafy.order.index') }}">
                                <i class="fas fa-list-check nav-icon"></i> الطلبات المتاحة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.offer.*') ? 'active' : '' }}"
                                href="{{ route('herafy.offer.index') }}">
                                <i class="fas fa-file-invoice-dollar nav-icon"></i> عروضي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.craftman.jobs.*') ? 'active' : '' }}"
                                href="{{ route('herafy.craftman.jobs.index') }}">
                                <i class="fas fa-briefcase nav-icon"></i> أعمالي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.craftman.portfolios.*') ? 'active' : '' }}"
                                href="{{ route('herafy.craftman.portfolios.index') }}">
                                <i class="fas fa-images nav-icon"></i> معرض أعمالي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('herafy.verification.*') ? 'active' : '' }}"
                                href="{{ route('herafy.verification.show') }}">
                                <i class="fas fa-shield-halved nav-icon"></i> التوثيق
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('showCraftsmen') ? 'active' : '' }}"
                                href="{{ route('showCraftsmen') }}">الحرفيون</a>
                        </li>
                    @endauth
                </ul>

                <div class="navbar-account ms-xl-auto d-flex align-items-center gap-2">
                    @if(auth('customer')->check() || auth('craftman')->check())
                        @php($notificationUser = auth('customer')->user() ?? auth('craftman')->user())
                        @php($notificationReadRoute = auth('customer')->check() ? 'herafy.customer.notifications.read' : 'herafy.craftman.notifications.read')
                        @php($notificationReadAllRoute = auth('customer')->check() ? 'herafy.customer.notifications.read-all' : 'herafy.craftman.notifications.read-all')
                        @php($notificationIndexRoute = auth('customer')->check() ? 'herafy.customer.notifications.index' : 'herafy.craftman.notifications.index')
                        @php($unreadNotificationCount = $notificationUser->unreadNotifications()->count())
                        @php($recentNotifications = $notificationUser->notifications()->latest()->limit(5)->get())
                        <div class="dropdown">
                            <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false" aria-label="الإشعارات">
                                <i class="fas fa-bell"></i>
                                @if($unreadNotificationCount > 0)
                                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger" data-testid="notification-unread-count">{{ $unreadNotificationCount }}</span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" style="width: min(360px, 90vw)">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                    <strong>الإشعارات</strong>
                                    @if($unreadNotificationCount > 0)
                                        <form method="POST" action="{{ route($notificationReadAllRoute) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">قراءة الكل</button>
                                        </form>
                                    @endif
                                </div>
                                @forelse($recentNotifications as $notification)
                                    <form method="POST" action="{{ route($notificationReadRoute, $notification->id) }}" class="border-bottom">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item text-wrap py-3 {{ $notification->read_at ? '' : 'bg-warning-subtle' }}">
                                            <strong class="d-block">{{ $notification->data['title'] ?? 'إشعار' }}</strong>
                                            <span class="small text-muted d-block">{{ $notification->data['message'] ?? '' }}</span>
                                            <small class="text-muted">{{ $notification->created_at?->diffForHumans() }}</small>
                                        </button>
                                    </form>
                                @empty
                                    <div class="text-center text-muted p-3">لا توجد إشعارات.</div>
                                @endforelse
                                <a class="dropdown-item text-center fw-bold py-2" href="{{ route($notificationIndexRoute) }}">عرض كل الإشعارات</a>
                            </div>
                        </div>
                    @endif
                    @if(auth('customer')->check() || auth('craftman')->check())
                        @php($navbarUser = (auth('customer')->user() ?? auth('craftman')->user())->fresh())
                        @php($navbarPhotoUrl = $navbarUser->profilePhotoUrl())
                        <div class="dropdown">
                            <button class="btn btn-light account-menu-button dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="account-avatar">
                                    @if($navbarPhotoUrl)
                                        <img src="{{ $navbarPhotoUrl }}" alt="" class="account-avatar-image">
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </span>
                                <span class="account-name">{{ $navbarUser->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li class="px-3 py-2 text-muted small">
                                    {{ auth('customer')->check() ? 'حساب عميل' : 'حساب حرفي' }}
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @auth('customer')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('main') }}">
                                            <i class="fas fa-gauge-high nav-icon"></i> لوحة التحكم
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.customer.profile.show') }}">
                                            <i class="fas fa-id-card nav-icon"></i> عرض الملف الشخصي
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.customer.profile.edit') }}">
                                            <i class="fas fa-user-pen nav-icon"></i> تعديل الملف الشخصي
                                        </a>
                                    </li>
                                @endauth
                                @auth('craftman')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.craftman.dashboard') }}">
                                            <i class="fas fa-gauge-high nav-icon"></i> لوحة التحكم
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.craftman.profile.show') }}">
                                            <i class="fas fa-id-card nav-icon"></i> عرض الملف الشخصي
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.craftman.portfolios.index') }}">
                                            <i class="fas fa-images nav-icon"></i> معرض أعمالي
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('herafy.craftman.profile.edit') }}">
                                            <i class="fas fa-user-pen nav-icon"></i> تعديل الملف الشخصي
                                        </a>
                                    </li>
                                @endauth
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout-action">
                                            <i class="fas fa-right-from-bracket nav-icon"></i> تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="d-flex flex-column flex-lg-row gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-right-to-bracket nav-icon"></i> دخول
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-warning btn-sm">انضم الآن</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    @endif

    

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/crud.js') }}"></script>
    @if(session('success'))
        <script>showMessage(@json(['icon' => 'success', 'title' => session('success')]));</script>
    @endif
    @if(session('error'))
        <script>showMessage(@json(['icon' => 'error', 'title' => session('error')]));</script>
    @endif

    <!-- ============================================ -->
    <!-- ملف JS الخاص بنا -->
    <!-- ============================================ -->
    <script src="{{asset('herafy/js/script.js')}}"></script>
    @yield('script')
</body>

</html>
