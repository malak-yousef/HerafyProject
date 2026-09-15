<nav class="navbar navbar-expand-xl navbar-light bg-white shadow-sm sticky-top herafy-navbar" aria-label="التنقل الرئيسي">
    <div class="container-fluid px-3 px-xl-5">
        <a class="navbar-brand fw-bold fs-3" href="{{ route('index') }}"><i class="fas fa-hammer text-warning" aria-hidden="true"></i> حرفي</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="فتح القائمة"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav align-items-xl-center gap-xl-1 me-xl-3 mb-3 mb-xl-0">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('index') ? 'active' : '' }}" href="{{ route('index') }}">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('index') }}#about">من نحن</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('showCraftsmen', 'herafy.craftmen.*') ? 'active' : '' }}" href="{{ route('showCraftsmen') }}">الحرفيون</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('index') }}#testimonials">الآراء</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('index') }}#gallery">معرض الأعمال</a></li>
            </ul>
            <div class="navbar-account ms-xl-auto d-flex flex-column flex-xl-row align-items-xl-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-warning btn-sm"><i class="fas fa-right-to-bracket nav-icon" aria-hidden="true"></i> دخول</a>
                <a href="{{ route('register') }}" class="btn btn-warning btn-sm">انضم الآن</a>
            </div>
        </div>
    </div>
</nav>
