<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trim($__env->yieldContent('title')) ?: 'لوحة التحكم' }} | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('cms/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @yield('styles')
</head>

<body>
    @php
    $active = fn (...$patterns) => request()->routeIs(...$patterns);
    $groups = [
    'users' => ['label' => 'المستخدمون', 'icon' => 'fa-users', 'open' => $active('admins.*', 'admins_*', 'customers.*', 'customers_*', 'craftmen.*', 'craftmen_*')],
    'marketplace' => ['label' => 'السوق', 'icon' => 'fa-store', 'open' => $active('orders.*', 'orders_*', 'offers.*', 'offers_*', 'service-jobs.*', 'service-jobs_*')],
    'moderation' => ['label' => 'الإشراف والمراجعة', 'icon' => 'fa-shield-halved', 'open' => $active('reviews.*', 'reviews_*', 'verifications.*', 'reports.*')],
    'content' => ['label' => 'التصنيفات والمحتوى', 'icon' => 'fa-layer-group', 'open' => $active('categories.*', 'categories_*', 'professions.*', 'professions_*', 'portfolios.*', 'portfolios_*')],
    'locations' => ['label' => 'المواقع', 'icon' => 'fa-location-dot', 'open' => $active('cities.*', 'cities_*', 'areas.*', 'areas_*')],
    'system' => ['label' => 'الصلاحيات والنظام', 'icon' => 'fa-gears', 'open' => $active('roles.*', 'permissions.*', 'settings.*')],
    ];
    $pageTitle = trim($__env->yieldContent('main_content')) ?: trim($__env->yieldContent('title')) ?: 'لوحة التحكم';
    $pageDescription = trim($__env->yieldContent('page_description'));
    $labels = ['admins'=>'المشرفون','customers'=>'العملاء','craftmen'=>'الحرفيون','orders'=>'الطلبات','offers'=>'العروض','service-jobs'=>'الأعمال','reviews'=>'التقييمات','verifications'=>'طلبات التوثيق','reports'=>'البلاغات','categories'=>'التصنيفات','professions'=>'المهن','portfolios'=>'الأعمال السابقة','cities'=>'المدن','areas'=>'المناطق','roles'=>'الأدوار','permissions'=>'الصلاحيات','settings'=>'الإعدادات'];
    $actions = ['create'=>'إضافة','show'=>'عرض','edit'=>'تعديل','trashed'=>'المحذوفات'];
    $routeName = request()->route()?->getName() ?? '';
    $parts = explode('.', $routeName);
    $resource = $parts[0] ?? '';
    $action = $parts[1] ?? '';
    if (! isset($labels[$resource])) {
    foreach (array_keys($labels) as $candidate) {
    if (str_starts_with($routeName, $candidate.'_')) { $resource = $candidate; break; }
    }
    }
    @endphp

    <button class="mobile-menu-toggle" type="button" data-sidebar-open aria-controls="cmsSidebar" aria-expanded="false" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
    <div class="sidebar-overlay" data-sidebar-overlay hidden></div>
    <aside class="sidebar" id="cmsSidebar" aria-label="القائمة الرئيسية">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}"><i class="fas fa-hammer"></i> {{ config('app.name') }}</a>
            <button type="button" class="sidebar-close" data-sidebar-close aria-label="إغلاق القائمة"><i class="fas fa-xmark"></i></button>
        </div>
        <nav class="sidebar-nav">
            @can('dashboard.view')<a data-nav-item="dashboard" class="nav-link {{ $active('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-gauge-high"></i><span>لوحة التحكم</span></a>@endcan

            @canany(['admins.view','customers.view','craftmen.view'])
            @include('cms.partials.sidebar-group', ['key'=>'users','group'=>$groups['users'],'items'=>[
            ['permission'=>'admins.view','route'=>'admins.index','patterns'=>['admins.*','admins_*'],'key'=>'admins','icon'=>'fa-user-shield','label'=>'المشرفون'],
            ['permission'=>'customers.view','route'=>'customers.index','patterns'=>['customers.*','customers_*'],'key'=>'customers','icon'=>'fa-user','label'=>'العملاء'],
            ['permission'=>'craftmen.view','route'=>'craftmen.index','patterns'=>['craftmen.*','craftmen_*'],'key'=>'craftmen','icon'=>'fa-user-gear','label'=>'الحرفيون']]])
            @endcanany
            @canany(['orders.view','offers.view','service-jobs.view'])
            @include('cms.partials.sidebar-group', ['key'=>'marketplace','group'=>$groups['marketplace'],'items'=>[
            ['permission'=>'orders.view','route'=>'orders.index','patterns'=>['orders.*','orders_*'],'key'=>'orders','icon'=>'fa-clipboard-list','label'=>'الطلبات'],
            ['permission'=>'offers.view','route'=>'offers.index','patterns'=>['offers.*','offers_*'],'key'=>'offers','icon'=>'fa-handshake','label'=>'العروض'],
            ['permission'=>'service-jobs.view','route'=>'service-jobs.index','patterns'=>['service-jobs.*','service-jobs_*'],'key'=>'service-jobs','icon'=>'fa-screwdriver-wrench','label'=>'الأعمال']]])
            @endcanany
            @canany(['reviews.view','verifications.view','reports.view'])
            @include('cms.partials.sidebar-group', ['key'=>'moderation','group'=>$groups['moderation'],'items'=>[
            ['permission'=>'reviews.view','route'=>'reviews.index','patterns'=>['reviews.*','reviews_*'],'key'=>'reviews','icon'=>'fa-star','label'=>'التقييمات'],
            ['permission'=>'verifications.view','route'=>'verifications.index','patterns'=>['verifications.*'],'key'=>'verifications','icon'=>'fa-circle-check','label'=>'طلبات التوثيق'],
            ['permission'=>'reports.view','route'=>'reports.index','patterns'=>['reports.*'],'key'=>'reports','icon'=>'fa-flag','label'=>'البلاغات']]])
            @endcanany
            @canany(['categories.view','professions.view','portfolios.view'])
            @include('cms.partials.sidebar-group', ['key'=>'content','group'=>$groups['content'],'items'=>[
            ['permission'=>'categories.view','route'=>'categories.index','patterns'=>['categories.*','categories_*'],'key'=>'categories','icon'=>'fa-tags','label'=>'التصنيفات'],
            ['permission'=>'professions.view','route'=>'professions.index','patterns'=>['professions.*','professions_*'],'key'=>'professions','icon'=>'fa-toolbox','label'=>'المهن'],
            ['permission'=>'portfolios.view','route'=>'portfolios.index','patterns'=>['portfolios.*','portfolios_*'],'key'=>'portfolios','icon'=>'fa-briefcase','label'=>'الأعمال السابقة']]])
            @endcanany
            @canany(['cities.view','areas.view'])
            @include('cms.partials.sidebar-group', ['key'=>'locations','group'=>$groups['locations'],'items'=>[
            ['permission'=>'cities.view','route'=>'cities.index','patterns'=>['cities.*','cities_*'],'key'=>'cities','icon'=>'fa-city','label'=>'المدن'],
            ['permission'=>'areas.view','route'=>'areas.index','patterns'=>['areas.*','areas_*'],'key'=>'areas','icon'=>'fa-map-location-dot','label'=>'المناطق']]])
            @endcanany
            @canany(['roles.manage','permissions.manage','settings.view'])
            @include('cms.partials.sidebar-group', ['key'=>'system','group'=>$groups['system'],'items'=>[
            ['permission'=>'roles.manage','route'=>'roles.index','patterns'=>['roles.*'],'key'=>'roles','icon'=>'fa-user-tag','label'=>'الأدوار'],
            ['permission'=>'permissions.manage','route'=>'permissions.index','patterns'=>['permissions.*'],'key'=>'permissions','icon'=>'fa-key','label'=>'الصلاحيات'],
            ['permission'=>'settings.view','route'=>'settings.index','patterns'=>['settings.*'],'key'=>'settings','icon'=>'fa-sliders','label'=>'الإعدادات']]])
            @endcanany
        </nav>
    </aside>

    <main class="main-content">
        <header class="cms-header">
            <div class="page-heading">
                <h1>{{ $pageTitle }}</h1>@if($pageDescription)<p>{{ $pageDescription }}</p>@endif
            </div>
            <div class="dropdown admin-account">
                <button class="admin-account-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="admin-avatar"><i class="fas fa-user-shield"></i></span>
                    <span data-testid="admin-account-name">{{ auth('admin')->user()->name ?? auth('admin')->user()->user_name ?? 'المشرف' }}</span>
                    <i class="fas fa-chevron-down account-chevron"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-start">
                    @can('dashboard.view')<li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-gauge-high"></i> لوحة التحكم</a></li>@endcan
                    <li><button class="dropdown-item text-danger" type="button" data-cms-logout><i class="fas fa-right-from-bracket"></i> تسجيل الخروج</button></li>
                </ul>
            </div>
        </header>
        <nav class="cms-breadcrumb" data-testid="cms-breadcrumb" aria-label="مسار التنقل">
            @hasSection('breadcrumbs') @yield('breadcrumbs')
            @else
            @if($active('dashboard')) <span aria-current="page"><i class="fas fa-house"></i> لوحة التحكم</span>
            @else
            <a href="{{ route('dashboard') }}"><i class="fas fa-house"></i> لوحة التحكم</a>
            @if(isset($labels[$resource])) <i class="fas fa-chevron-left separator"></i><span>{{ $labels[$resource] }}</span>@endif
            @if(isset($actions[$action])) <i class="fas fa-chevron-left separator"></i><span aria-current="page">{{ $actions[$action] }}</span>@endif
            @endif
            @endif
        </nav>
        <div class="cms-content">@yield('content')</div>
    </main>

    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/crud.js') }}"></script>
    @if(session('success'))
        <script>showMessage(@json(['icon' => 'success', 'title' => session('success')]));</script>
    @endif
    @if(session('error'))
        <script>showMessage(@json(['icon' => 'error', 'title' => session('error')]));</script>
    @endif
    <script>
        (() => {
            const sidebar = document.getElementById('cmsSidebar');
            const overlay = document.querySelector('[data-sidebar-overlay]');
            const opener = document.querySelector('[data-sidebar-open]');
            const close = () => {
                sidebar.classList.remove('mobile-open');
                overlay.hidden = true;
                document.body.classList.remove('sidebar-open');
                opener.setAttribute('aria-expanded', 'false');
            };
            const open = () => {
                sidebar.classList.add('mobile-open');
                overlay.hidden = false;
                document.body.classList.add('sidebar-open');
                opener.setAttribute('aria-expanded', 'true');
            };
            opener.addEventListener('click', open);
            document.querySelector('[data-sidebar-close]').addEventListener('click', close);
            overlay.addEventListener('click', close);
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') close();
            });
            document.querySelectorAll('.nav-group-toggle').forEach(toggle => toggle.addEventListener('click', () => {
                const group = toggle.closest('.nav-group');
                const submenu = group.querySelector('.submenu');
                const willOpen = !group.classList.contains('open');
                group.classList.toggle('open', willOpen);
                submenu.hidden = !willOpen;
                toggle.setAttribute('aria-expanded', String(willOpen));
            }));
            document.querySelectorAll('[data-cms-logout]').forEach(button => button.addEventListener('click', () => {
                Swal.fire({
                    title: 'تسجيل الخروج',
                    text: 'هل تريد تسجيل الخروج؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f1c40f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم',
                    cancelButtonText: 'إلغاء'
                }).then(result => {
                    if (result.isConfirmed) document.getElementById('logoutForm').submit();
                });
            }));
            const mutate = (url, method) => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}"><input type="hidden" name="_method" value="${method}">`;
                document.body.appendChild(form);
                form.submit();
            };
            document.addEventListener('click', event => {
                const link = event.target.closest('a[href]');
                if (!link) return;
                const url = new URL(link.href, window.location.origin);
                const restore = /_restore\//.test(url.pathname);
                const destructive = /_(force|forceAll|force_destroy|destroy_all)(\/|$)/.test(url.pathname);
                if (!restore && !destructive) return;
                event.preventDefault();
                event.stopImmediatePropagation();
                mutate(url.href, restore ? 'PATCH' : 'DELETE');
            }, true);
        })();
    </script>
    @yield('scripts')
</body>

</html>
