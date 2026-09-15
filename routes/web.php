<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CraftmanController;
use App\Http\Controllers\CraftmanDashboardController;
use App\Http\Controllers\CraftmanDiscoveryController;
use App\Http\Controllers\CraftmanNotificationController;
use App\Http\Controllers\CraftmanOrderController;
use App\Http\Controllers\CraftmanPortfolioController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerNotificationController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\HerafyHomeController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderImageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioImageController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceJobController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\VerificationController;
// use App\Models\Category;
use Illuminate\Support\Facades\Route;

// ===============================
// Verifications - Craftman
// ===============================
Route::middleware('auth:craftman')->group(function () {

    Route::get('herafy/craftman/dashboard', [CraftmanDashboardController::class, 'show'])
        ->name('herafy.craftman.dashboard');
    Route::get('herafy/craftman/profile', [CraftmanDashboardController::class, 'profile'])
        ->name('herafy.craftman.profile.show');
    Route::get('herafy/craftman/profile/edit', [CraftmanDashboardController::class, 'editProfile'])
        ->name('herafy.craftman.profile.edit');
    Route::patch('herafy/craftman/profile', [CraftmanDashboardController::class, 'updateProfile'])
        ->name('herafy.craftman.profile.update');
    Route::get('herafy/craftman/notifications', [CraftmanNotificationController::class, 'index'])
        ->name('herafy.craftman.notifications.index');
    Route::patch('herafy/craftman/notifications/read-all', [CraftmanNotificationController::class, 'readAll'])
        ->name('herafy.craftman.notifications.read-all');
    Route::patch('herafy/craftman/notifications/{notification}/read', [CraftmanNotificationController::class, 'read'])
        ->name('herafy.craftman.notifications.read');
    Route::get('herafy/craftman/portfolio', [CraftmanPortfolioController::class, 'index'])->name('herafy.craftman.portfolios.index');
    Route::get('herafy/craftman/portfolio/create', [CraftmanPortfolioController::class, 'create'])->name('herafy.craftman.portfolios.create');
    Route::post('herafy/craftman/portfolio', [CraftmanPortfolioController::class, 'store'])->name('herafy.craftman.portfolios.store');
    Route::get('herafy/craftman/portfolio/{portfolio}/edit', [CraftmanPortfolioController::class, 'edit'])->name('herafy.craftman.portfolios.edit');
    Route::put('herafy/craftman/portfolio/{portfolio}', [CraftmanPortfolioController::class, 'update'])->name('herafy.craftman.portfolios.update');
    Route::delete('herafy/craftman/portfolio/{portfolio}', [CraftmanPortfolioController::class, 'destroy'])->name('herafy.craftman.portfolios.destroy');

    Route::get(
        'herafy/verification/create',
        [VerificationController::class, 'create']
    )->name('herafy.verification.create');

    Route::post(
        'herafy/verifications',
        [VerificationController::class, 'store']
    )->name('herafy.verification.store');

    Route::get(
        'herafy/verification',
        [VerificationController::class, 'myVerification']
    )->name('herafy.verification.show');

    // Orders - Craftman
    // =========================

    Route::get(
        'herafy/orders',
        [CraftmanOrderController::class, 'index']
    )->name('herafy.order.index');

    Route::get(
        'herafy/orders/{order}',
        [CraftmanOrderController::class, 'show']
    )->name('herafy.order.show');

    // =========================
    // Offers - Craftman
    // =========================

    Route::get(
        'herafy/offers',
        [OfferController::class, 'craftmanOffers']
    )->name('herafy.offer.index');

    Route::get(
        'herafy/offers/create/{order}',
        [OfferController::class, 'craftmanCreate']
    )->name('herafy.offer.create');

    Route::post(
        'herafy/orders/{order}/offers',
        [OfferController::class, 'craftmanStore']
    )->name('herafy.offer.store');

    Route::get(
        'herafy/offers/{offer}',
        [OfferController::class, 'craftmanShow']
    )->name('herafy.offer.show');

    Route::get('herafy/my-jobs', [ServiceJobController::class, 'craftmanIndex'])
        ->name('herafy.craftman.jobs.index');
    Route::get('herafy/my-jobs/{serviceJob}', [ServiceJobController::class, 'craftmanShow'])
        ->name('herafy.craftman.jobs.show');
    Route::patch('herafy/my-jobs/{serviceJob}/start', [ServiceJobController::class, 'craftmanStart'])
        ->name('herafy.craftman.jobs.start');
    Route::patch('herafy/my-jobs/{serviceJob}/complete', [ServiceJobController::class, 'craftmanComplete'])
        ->name('herafy.craftman.jobs.complete');
    Route::post('herafy/my-jobs/{serviceJob}/customer-review', [ReviewController::class, 'craftmanStoreCustomerReview'])
        ->name('herafy.craftman.jobs.customer-review.store');
});
// صفحات المشروع
Route::prefix('herafy')->middleware('auth:customer')->group(function () {
    Route::get('profile', [CustomerProfileController::class, 'show'])->name('herafy.customer.profile.show');
    Route::get('profile/edit', [CustomerProfileController::class, 'edit'])->name('herafy.customer.profile.edit');
    Route::patch('profile', [CustomerProfileController::class, 'update'])->name('herafy.customer.profile.update');
    Route::get('notifications', [CustomerNotificationController::class, 'index'])->name('herafy.customer.notifications.index');
    Route::patch('notifications/read-all', [CustomerNotificationController::class, 'readAll'])->name('herafy.customer.notifications.read-all');
    Route::patch('notifications/{notification}/read', [CustomerNotificationController::class, 'read'])->name('herafy.customer.notifications.read');
    Route::get('request', [CustomerOrderController::class, 'create'])->name('request');
    Route::post('orders', [CustomerOrderController::class, 'store'])->name('herafy.customer.orders.store');
    Route::get('my-orders', [CustomerOrderController::class, 'index'])->name('herafy.customer.orders.index');
    Route::get('my-orders/{order}', [CustomerOrderController::class, 'show'])->name('herafy.customer.orders.show');
    Route::patch('my-orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('herafy.customer.orders.cancel');
    Route::patch('my-orders/{order}/offers/{offer}/accept', [CustomerOrderController::class, 'acceptOffer'])->name('herafy.customer.orders.offers.accept');
    Route::post('my-jobs/{serviceJob}/review', [ReviewController::class, 'customerStore'])
        ->name('herafy.customer.jobs.review.store');
});

Route::prefix('herafy')->group(function () {
    Route::get('main', [CustomerProfileController::class, 'home'])->name('main');
    Route::get('showCraftsmen', [CraftmanDiscoveryController::class, 'index'])->name('showCraftsmen');
    Route::get('craftsmen/{craftman}', [CraftmanDiscoveryController::class, 'show'])->name('herafy.craftmen.show');
    Route::get('craftsmen/{craftman}/portfolio/{portfolio}', [CraftmanDiscoveryController::class, 'portfolio'])->name('herafy.craftmen.portfolios.show');
});

// تسجيل الدخول
Route::middleware('guest:admin,customer,craftman')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('auth/google', [AuthController::class, 'redirectToGoogle'])
        ->middleware('throttle:20,1')
        ->name('auth.google.redirect');
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
        ->middleware('throttle:20,1')
        ->name('auth.google.callback');

    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])
        ->middleware('guest:web')
        ->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware(['guest:web', 'throttle:5,1'])
        ->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->middleware('guest:web')
        ->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->middleware(['guest:web', 'throttle:5,1'])
        ->name('password.update');

    // انشاء حساب
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:registration');
});

// تسجيل الخروج
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:web,admin,customer,craftman');

// الصفحة الرئيسية
Route::prefix('cms/admin')->middleware('auth:admin')->group(function () {
    Route::get('', [AdminDashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('can:dashboard.view');
    Route::redirect('temp', '/cms/admin')->name('cms.main')->middleware('can:dashboard.view');

    // الحرفيين
    Route::resource('craftmen', CraftmanController::class);
    Route::post('craftmen_update/{id}', [CraftmanController::class, 'update'])->name('craftmen_update');
    Route::get('craftmen_trashed', [CraftmanController::class, 'trashed'])->name('craftmen_trashed');
    Route::patch('craftmen_restore/{id}', [CraftmanController::class, 'restore'])->name('craftmen_restore');
    Route::delete('craftmen_force_destroy/{id}', [CraftmanController::class, 'forceDestroy'])->name('craftmen_force_destroy');
    Route::delete('craftmen_destroy_all', [CraftmanController::class, 'DestroyAll'])->name('craftmen_destroy_all');

    // الاقسام
    Route::resource('categories', CategoryController::class);
    Route::post('categories_update/{id}', [CategoryController::class, 'update'])->name('categories_update');
    Route::get('categories_trashed', [CategoryController::class, 'trashed'])->name('categories_trashed');
    Route::patch('categories_restore/{id}', [CategoryController::class, 'restore'])->name('categories_restore');
    Route::delete('categories_force/{id}', [CategoryController::class, 'force'])->name('categories_force');
    Route::delete('categories_forceAll', [CategoryController::class, 'forceAll'])->name('categories_forceAll');

    // المهن
    Route::resource('professions', ProfessionController::class);
    Route::post('professions_update/{id}', [ProfessionController::class, 'update'])->name('professions_update');
    Route::get('professions_trashed', [ProfessionController::class, 'trashed'])->name('professions_trashed');
    Route::patch('professions_restore/{id}', [ProfessionController::class, 'restore'])->name('professions_restore');
    Route::delete('professions_force/{id}', [ProfessionController::class, 'force'])->name('professions_force');
    Route::delete('professions_forceAll', [ProfessionController::class, 'forceAll'])->name('professions_forceAll');

    // الادمن
    Route::resource('admins', AdminController::class);
    Route::post('admins_update/{id}', [AdminController::class, 'update'])->name('admins_update');
    Route::get('admins_trashed', [AdminController::class, 'trashed'])->name('admins_trashed');
    Route::patch('admins_restore/{id}', [AdminController::class, 'restore'])->name('admins_restore');
    Route::delete('admins_force/{id}', [AdminController::class, 'force'])->name('admins_force');
    Route::delete('admins_forceAll', [AdminController::class, 'forceAll'])->name('admins_forceAll');

    // معرض الاعمال
    Route::resource('portfolios', PortfolioController::class);
    Route::post('portfolios_update/{id}', [PortfolioController::class, 'update'])->name('portfolios_update');
    Route::get('portfolios_trashed', [PortfolioController::class, 'trashed'])->name('portfolios_trashed');
    Route::patch('portfolios_restore/{id}', [PortfolioController::class, 'restore'])->name('portfolios_restore');
    Route::delete('portfolios_force_destroy/{id}', [PortfolioController::class, 'forceDestroy'])->name('portfolios_force_destroy');
    Route::delete('portfolios_destroy_all', [PortfolioController::class, 'DestroyAll'])->name('portfolios_destroy_all');

    // صور معرض الاعمال
    Route::resource('portfolio_images', PortfolioImageController::class);
    Route::post('portfolio_images_update/{id}', [PortfolioImageController::class, 'update'])->name('portfolio_images_update');
    Route::get('portfolio_images_trashed', [PortfolioImageController::class, 'trashed'])->name('portfolio_images_trashed');
    Route::patch('portfolio_images_restore/{id}', [PortfolioImageController::class, 'restore'])->name('portfolio_images_restore');
    Route::delete('portfolio_images_force_destroy/{id}', [PortfolioImageController::class, 'forceDestroy'])->name('portfolio_images_force_destroy');
    Route::delete('portfolio_images_destroy_all', [PortfolioImageController::class, 'DestroyAll'])->name('portfolio_images_destroy_all');

    // العملاء
    Route::resource('customers', CustomerController::class);
    Route::post('customers_update/{id}', [CustomerController::class, 'update'])->name('customers_update');
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])
        ->name('customers.orders');
    Route::get('customers_trashed', [CustomerController::class, 'trashed'])->name('customers_trashed');
    Route::patch('customers_restore/{id}', [CustomerController::class, 'restore'])->name('customers_restore');
    Route::delete('customers_force/{id}', [CustomerController::class, 'force'])->name('customers_force');
    Route::delete('customers_forceAll', [CustomerController::class, 'forceAll'])->name('customers_forceAll');

    // الصلاحيات
    Route::resource('roles', RoleController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::resource('permissions', PermissionController::class)->only(['index', 'create', 'store', 'destroy']);

    // الصلاحيات
    Route::get('roles/{id}/permissions', [RoleController::class, 'editRolePermissions'])->name('roles.edit-permissions');
    Route::post('roles/{id}/permissions', [RoleController::class, 'updateRolePermissions'])->name('roles.update-permissions');

    // المناطق
    Route::resource('areas', AreaController::class);
    Route::post('areas_update/{id}', [AreaController::class, 'update'])->name('areas_update');
    Route::get('areas_trashed', [AreaController::class, 'trashed'])->name('areas_trashed');
    Route::patch('areas_restore/{id}', [AreaController::class, 'restore'])->name('areas_restore');
    Route::delete('areas_force/{id}', [AreaController::class, 'force'])->name('areas_force');
    Route::delete('areas_forceAll', [AreaController::class, 'forceAll'])->name('areas_forceAll');

    // المدن
    Route::resource('cities', CityController::class);
    Route::post('cities_update/{id}', [CityController::class, 'update'])->name('cities_update');
    Route::get('cities_trashed', [CityController::class, 'trashed'])->name('cities_trashed');
    Route::patch('cities_restore/{id}', [CityController::class, 'restore'])->name('cities_restore');
    Route::delete('cities_force/{id}', [CityController::class, 'force'])->name('cities_force');
    Route::delete('cities_forceAll', [CityController::class, 'forceAll'])->name('cities_forceAll');

    // الطلبات
    Route::resource('orders', OrderController::class);
    Route::post('orders_update/{id}', [OrderController::class, 'update'])->name('orders_update');
    Route::get('orders_trashed', [OrderController::class, 'trashed'])->name('orders_trashed');
    Route::patch('orders_restore/{id}', [OrderController::class, 'restore'])->name('orders_restore');
    Route::delete('orders_force/{id}', [OrderController::class, 'force'])->name('orders_force');
    Route::delete('orders_forceAll', [OrderController::class, 'forceAll'])->name('orders_forceAll');

    // التقييمات
    Route::resource('reviews', ReviewController::class);
    Route::post('reviews_update/{id}', [ReviewController::class, 'update'])->name('reviews_update');
    Route::get('reviews_trashed', [ReviewController::class, 'trashed'])->name('reviews_trashed');
    Route::patch('reviews_restore/{id}', [ReviewController::class, 'restore'])->name('reviews_restore');
    Route::delete('reviews_force/{id}', [ReviewController::class, 'force'])->name('reviews_force');
    Route::delete('reviews_forceAll', [ReviewController::class, 'forceAll'])->name('reviews_forceAll');

    Route::resource('order_images', OrderImageController::class);
    Route::post('order_images_update/{id}', [OrderImageController::class, 'update'])->name('order_images_update');
    Route::get('order_images_trashed', [OrderImageController::class, 'trashed'])->name('order_images_trashed');
    Route::patch('order_images_restore/{id}', [OrderImageController::class, 'restore'])->name('order_images_restore');
    Route::delete('order_images_force_destroy/{id}', [OrderImageController::class, 'forceDestroy'])->name('order_images_force_destroy');
    Route::delete('order_images_destroy_all', [OrderImageController::class, 'DestroyAll'])->name('order_images_destroy_all');

    Route::resource('offers', OfferController::class);
    Route::post('offers_update/{id}', [OfferController::class, 'update'])->name('offers_update');
    Route::get('offers_trashed', [OfferController::class, 'trashed'])->name('offers_trashed');
    Route::patch('offers_restore/{id}', [OfferController::class, 'restore'])->name('offers_restore');
    Route::delete('offers_force/{id}', [OfferController::class, 'force'])->name('offers_force');
    Route::delete('offers_forceAll', [OfferController::class, 'forceAll'])->name('offers_forceAll');

    Route::get(
        'service-jobs/trashed',
        [ServiceJobController::class, 'trashed']
    )->name('service-jobs.trashed');
    Route::resource('service-jobs', ServiceJobController::class);
    Route::post('service-jobs_update/{id}', [ServiceJobController::class, 'update'])->name('service-jobs_update');

    Route::patch('service-jobs/{id}/restore', [
        ServiceJobController::class,
        'restore',
    ])->name('service-jobs.restore');

    Route::delete('service-jobs/{id}/force-delete', [
        ServiceJobController::class,
        'forceDelete',
    ])->name('service-jobs.force-delete');

    Route::resource('settings', SettingController::class);

    Route::get('settings-trashed', [SettingController::class, 'trashed'])
        ->name('settings.trashed');

    Route::patch('settings/{id}/restore', [SettingController::class, 'restore'])
        ->name('settings.restore');

    Route::delete('settings/{id}/force-delete', [SettingController::class, 'forceDelete'])
        ->name('settings.forceDelete');

    Route::resource('reports', ReportController::class);

    Route::get(
        'reports-trashed',
        [ReportController::class, 'trashed']
    )->name('reports.trashed');

    Route::put(
        'reports/{id}/restore',
        [ReportController::class, 'restore']
    )->name('reports.restore');

    Route::delete('reports/{id}/force-delete', [ReportController::class, 'forceDelete'])
        ->name('reports.forceDelete');
    Route::post(
        'reports/user-store',
        [ReportController::class, 'userStore']
    )->name('reports.user_store');
    // Verifications - Admin
    Route::get('verifications', [VerificationController::class, 'index'])
        ->name('verifications.index');

    Route::get('verifications/trashed', [VerificationController::class, 'trashed'])
        ->name('verifications.trashed');

    Route::get('verifications/{verification}', [VerificationController::class, 'show'])
        ->name('verifications.show');

    Route::get('verifications/{verification}/edit', [VerificationController::class, 'edit'])
        ->name('verifications.edit');

    Route::put('verifications/{verification}', [VerificationController::class, 'update'])
        ->name('verifications.update');

    Route::delete('verifications/{verification}', [VerificationController::class, 'destroy'])
        ->name('verifications.destroy');

    Route::patch('verifications/{id}/restore', [VerificationController::class, 'restore'])
        ->name('verifications.restore');

    Route::getRoutes()->refreshNameLookups();

    $protectRoutes = function (array $routeNames, string $permission): void {
        foreach ($routeNames as $routeName) {
            Route::getRoutes()->getByName($routeName)->middleware("can:{$permission}");
        }
    };

    $resources = [
        'admins' => 'admins',
        'customers' => 'customers',
        'craftmen' => 'craftmen',
        'categories' => 'categories',
        'professions' => 'professions',
        'portfolios' => 'portfolios',
        'portfolio_images' => 'portfolio-images',
        'areas' => 'areas',
        'cities' => 'cities',
        'orders' => 'orders',
        'reviews' => 'reviews',
        'order_images' => 'order-images',
        'offers' => 'offers',
        'service-jobs' => 'service-jobs',
        'reports' => 'reports',
    ];

    foreach ($resources as $routeName => $permission) {
        $protectRoutes(["{$routeName}.index", "{$routeName}.show"], "{$permission}.view");
        $protectRoutes(["{$routeName}.create", "{$routeName}.store"], "{$permission}.create");
        $protectRoutes(["{$routeName}.edit", "{$routeName}.update"], "{$permission}.update");
        $protectRoutes(["{$routeName}.destroy"], "{$permission}.delete");
    }

    $protectRoutes(['craftmen_update'], 'craftmen.update');
    $protectRoutes(['craftmen_trashed'], 'craftmen.view');
    $protectRoutes(['craftmen_restore', 'craftmen_force_destroy', 'craftmen_destroy_all'], 'craftmen.delete');
    $protectRoutes(['categories_update'], 'categories.update');
    $protectRoutes(['categories_trashed'], 'categories.view');
    $protectRoutes(['categories_restore', 'categories_force', 'categories_forceAll'], 'categories.delete');
    $protectRoutes(['professions_update'], 'professions.update');
    $protectRoutes(['professions_trashed'], 'professions.view');
    $protectRoutes(['professions_restore', 'professions_force', 'professions_forceAll'], 'professions.delete');
    $protectRoutes(['admins_update'], 'admins.update');
    $protectRoutes(['admins_trashed'], 'admins.view');
    $protectRoutes(['admins_restore', 'admins_force', 'admins_forceAll'], 'admins.delete');
    $protectRoutes(['portfolios_update'], 'portfolios.update');
    $protectRoutes(['portfolios_trashed'], 'portfolios.view');
    $protectRoutes(['portfolios_restore', 'portfolios_force_destroy', 'portfolios_destroy_all'], 'portfolios.delete');
    $protectRoutes(['portfolio_images_update'], 'portfolio-images.update');
    $protectRoutes(['portfolio_images_trashed'], 'portfolio-images.view');
    $protectRoutes(['portfolio_images_restore', 'portfolio_images_force_destroy', 'portfolio_images_destroy_all'], 'portfolio-images.delete');
    $protectRoutes(['customers.orders', 'customers_trashed'], 'customers.view');
    $protectRoutes(['customers_update'], 'customers.update');
    $protectRoutes(['customers_restore', 'customers_force', 'customers_forceAll'], 'customers.delete');
    $protectRoutes(['areas_update'], 'areas.update');
    $protectRoutes(['areas_trashed'], 'areas.view');
    $protectRoutes(['areas_restore', 'areas_force', 'areas_forceAll'], 'areas.delete');
    $protectRoutes(['cities_update'], 'cities.update');
    $protectRoutes(['cities_trashed'], 'cities.view');
    $protectRoutes(['cities_restore', 'cities_force', 'cities_forceAll'], 'cities.delete');
    $protectRoutes(['orders_update'], 'orders.update');
    $protectRoutes(['orders_trashed'], 'orders.view');
    $protectRoutes(['orders_restore', 'orders_force', 'orders_forceAll'], 'orders.delete');
    $protectRoutes(['reviews_update'], 'reviews.update');
    $protectRoutes(['reviews_trashed'], 'reviews.view');
    $protectRoutes(['reviews_restore', 'reviews_force', 'reviews_forceAll'], 'reviews.delete');
    $protectRoutes(['order_images_update'], 'order-images.update');
    $protectRoutes(['order_images_trashed'], 'order-images.view');
    $protectRoutes(['order_images_restore', 'order_images_force_destroy', 'order_images_destroy_all'], 'order-images.delete');
    $protectRoutes(['offers_update'], 'offers.update');
    $protectRoutes(['offers_trashed'], 'offers.view');
    $protectRoutes(['offers_restore', 'offers_force', 'offers_forceAll'], 'offers.delete');
    $protectRoutes(['service-jobs_update'], 'service-jobs.update');
    $protectRoutes(['service-jobs.trashed'], 'service-jobs.view');
    $protectRoutes(['service-jobs.restore', 'service-jobs.force-delete'], 'service-jobs.delete');
    $protectRoutes(['reports.trashed'], 'reports.view');
    $protectRoutes(['reports.user_store'], 'reports.create');
    $protectRoutes(['reports.restore', 'reports.forceDelete'], 'reports.delete');
    $protectRoutes(['verifications.index', 'verifications.trashed', 'verifications.show'], 'verifications.view');
    $protectRoutes(['verifications.edit', 'verifications.update'], 'verifications.update');
    $protectRoutes(['verifications.destroy', 'verifications.restore'], 'verifications.delete');
    $protectRoutes(['roles.index', 'roles.create', 'roles.store', 'roles.destroy', 'roles.edit-permissions', 'roles.update-permissions'], 'roles.manage');
    $protectRoutes(['permissions.index', 'permissions.create', 'permissions.store', 'permissions.destroy'], 'permissions.manage');
    $protectRoutes(['settings.index', 'settings.create', 'settings.store', 'settings.show', 'settings.edit', 'settings.update', 'settings.destroy', 'settings.trashed', 'settings.restore', 'settings.forceDelete'], 'settings.manage');
});

Route::get('/', HerafyHomeController::class)->name('index');
