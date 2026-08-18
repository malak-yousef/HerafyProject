<?php

use App\Http\Controllers\{
    AdminController,
    CategoryController,
    CraftmanController,
    PortfolioController,
    ProfessionController,
    PortfolioImageController,
    CustomerController,
    AuthController,
    RoleController,
    PermissionController,
    RegisterController,
    AreaController,
    CityController,
    OfferController,
    OrderController,

    OrderImageController,
    ServiceJobController,
    VerificationController,
    SettingController,
ReportController,
CraftmanOrderController,
};

//use App\Models\Category;
use Illuminate\Support\Facades\Route;

// ===============================
// Verifications - Craftman
// ===============================
Route::prefix('herafy')->name('herafy.')->group(function () {

    Route::resource('offers', OfferController::class);

});
Route::middleware('auth:craftman')->group(function () {

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

});
// صفحات المشروع
Route::prefix('herafy')->group(function () {
    Route::view('main', 'herafy.parent')->name('main');
    Route::view('index', 'herafy.index')->name('index');
    Route::view('showCraftsmen', 'herafy.craftsmen')->name('showCraftsmen');
    Route::view('request', 'herafy.request')->name('request');
});


// تسجيل الدخول
Route::middleware('guest:admin,customer')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// انشاء حساب
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);

// تسجيل الخروج
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:admin,craftman,customer');

// الصفحة الرئيسية
Route::prefix('cms/admin')->middleware('auth:admin,customer')->group(function () {
    Route::view('', 'cms.parent');
    Route::view('temp', 'cms.temp')->name('main');

    // الحرفيين
    Route::resource('craftmen', CraftmanController::class);
    Route::post('craftmen_update/{id}', [CraftmanController::class, 'update'])->name('craftmen_update');
    Route::get('craftmen_trashed', [CraftmanController::class, 'trashed'])->name('craftmen_trashed');
    Route::get('craftmen_restore/{id}', [CraftmanController::class, 'restore'])->name('craftmen_restore');
    Route::get('craftmen_force_destroy/{id}', [CraftmanController::class, 'forceDestroy'])->name('craftmen_force_destroy');
    Route::get('craftmen_destroy_all', [CraftmanController::class, 'DestroyAll'])->name('craftmen_destroy_all');

    // الاقسام
    Route::resource('categories', CategoryController::class);
    Route::post('categories_update/{id}', [CategoryController::class, 'update'])->name('categories_update');
    Route::get('categories_trashed', [CategoryController::class, 'trashed'])->name('categories_trashed');
    Route::get('categories_restore/{id}', [CategoryController::class, 'restore'])->name('categories_restore');
    Route::get('categories_force/{id}', [CategoryController::class, 'force'])->name('categories_force');
    Route::get('categories_forceAll', [CategoryController::class, 'forceAll'])->name('categories_forceAll');

    // المهن
    Route::resource('professions', ProfessionController::class);
    Route::post('professions_update/{id}', [ProfessionController::class, 'update'])->name('professions_update');
    Route::get('professions_trashed', [ProfessionController::class, 'trashed'])->name('professions_trashed');
    Route::get('professions_restore/{id}', [ProfessionController::class, 'restore'])->name('professions_restore');
    Route::get('professions_force/{id}', [ProfessionController::class, 'force'])->name('professions_force');
    Route::get('professions_forceAll', [ProfessionController::class, 'forceAll'])->name('professions_forceAll');

    // الادمن
    Route::resource('admins', AdminController::class);
    Route::post('admins_update/{id}', [AdminController::class, 'update'])->name('admins_update');
    Route::get('admins_trashed', [AdminController::class, 'trashed'])->name('admins_trashed');
    Route::get('admins_restore/{id}', [AdminController::class, 'restore'])->name('admins_restore');
    Route::get('admins_force/{id}', [AdminController::class, 'force'])->name('admins_force');
    Route::get('admins_forceAll', [AdminController::class, 'forceAll'])->name('admins_forceAll');

    // معرض الاعمال
    Route::resource('portfolios', PortfolioController::class);
    Route::post('portfolios_update/{id}', [PortfolioController::class, 'update'])->name('portfolios_update');
    Route::get('portfolios_trashed', [PortfolioController::class, 'trashed'])->name('portfolios_trashed');
    Route::get('portfolios_restore/{id}', [PortfolioController::class, 'restore'])->name('portfolios_restore');
    Route::get('portfolios_force_destroy/{id}', [PortfolioController::class, 'forceDestroy'])->name('portfolios_force_destroy');
    Route::get('portfolios_destroy_all', [PortfolioController::class, 'DestroyAll'])->name('portfolios_destroy_all');

    // صور معرض الاعمال
    Route::resource('portfolio_images', PortfolioImageController::class);
    Route::post('portfolio_images_update/{id}', [PortfolioImageController::class, 'update'])->name('portfolio_images_update');
    Route::get('portfolio_images_trashed', [PortfolioImageController::class, 'trashed'])->name('portfolio_images_trashed');
    Route::get('portfolio_images_restore/{id}', [PortfolioImageController::class, 'restore'])->name('portfolio_images_restore');
    Route::get('portfolio_images_force_destroy/{id}', [PortfolioImageController::class, 'forceDestroy'])->name('portfolio_images_force_destroy');
    Route::get('portfolio_images_destroy_all', [PortfolioImageController::class, 'DestroyAll'])->name('portfolio_images_destroy_all');

    // العملاء
    Route::resource('customers', CustomerController::class);
    Route::post('customers_update/{id}', [CustomerController::class, 'update'])->name('customers_update');
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])
        ->name('customers.orders');
    Route::get('customers_trashed', [CustomerController::class, 'trashed'])->name('customers_trashed');
    Route::get('customers_restore/{id}', [CustomerController::class, 'restore'])->name('customers_restore');
    Route::get('customers_force/{id}', [CustomerController::class, 'force'])->name('customers_force');
    Route::get('customers_forceAll', [CustomerController::class, 'forceAll'])->name('customers_forceAll');

    // الصلاحيات
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // الصلاحيات
    Route::get('roles/{id}/permissions', [RoleController::class, 'editRolePermissions'])->name('roles.edit-permissions');
    Route::post('roles/{id}/permissions', [RoleController::class, 'updateRolePermissions'])->name('roles.update-permissions');

    // المناطق
    Route::resource('areas', AreaController::class);
    Route::post('areas_update/{id}', [AreaController::class, 'update'])->name('areas_update');
    Route::get('areas_trashed', [AreaController::class, 'trashed'])->name('areas_trashed');
    Route::get('areas_restore/{id}', [AreaController::class, 'restore'])->name('areas_restore');
    Route::get('areas_force/{id}', [AreaController::class, 'force'])->name('areas_force');
    Route::get('areas_forceAll', [AreaController::class, 'forceAll'])->name('areas_forceAll');

    // المدن
    Route::resource('cities', CityController::class);
    Route::post('cities_update/{id}', [CityController::class, 'update'])->name('cities_update');
    Route::get('cities_trashed', [CityController::class, 'trashed'])->name('cities_trashed');
    Route::get('cities_restore/{id}', [CityController::class, 'restore'])->name('cities_restore');
    Route::get('cities_force/{id}', [CityController::class, 'force'])->name('cities_force');
    Route::get('cities_forceAll', [CityController::class, 'forceAll'])->name('cities_forceAll');

    // الطلبات
    Route::resource('orders', OrderController::class);
    Route::post('orders_update/{id}', [OrderController::class, 'update'])->name('orders_update');
    Route::get('orders_trashed', [OrderController::class, 'trashed'])->name('orders_trashed');
    Route::get('orders_restore/{id}', [OrderController::class, 'restore'])->name('orders_restore');
    Route::get('orders_force/{id}', [OrderController::class, 'force'])->name('orders_force');
    Route::get('orders_forceAll', [OrderController::class, 'forceAll'])->name('orders_forceAll');


    

      Route::resource('order_images', OrderImageController::class);
    Route::post('order_images_update/{id}', [OrderImageController::class, 'update'])->name('order_images_update');
    Route::get('order_images_trashed', [OrderImageController::class, 'trashed'])->name('order_images_trashed');
    Route::get('order_images_restore/{id}', [OrderImageController::class, 'restore'])->name('order_images_restore');
    Route::get('order_images_force_destroy/{id}', [OrderImageController::class, 'forceDestroy'])->name('order_images_force_destroy');
    Route::get('order_images_destroy_all', [OrderImageController::class, 'DestroyAll'])->name('order_images_destroy_all');

    Route::resource('offers', OfferController::class);
    Route::post('offers_update/{id}', [OfferController::class, 'update'])->name('offers_update');
    Route::get('offers_trashed', [OfferController::class, 'trashed'])->name('offers_trashed');
    Route::get('offers_restore/{id}', [OfferController::class, 'restore'])->name('offers_restore');
    Route::get('offers_force/{id}', [OfferController::class, 'force'])->name('offers_force');
    Route::get('offers_forceAll', [OfferController::class, 'forceAll'])->name('offers_forceAll');

 Route::get('service-jobs/trashed', 
    [ServiceJobController::class, 'trashed']
)->name('service-jobs.trashed');
Route::resource('service-jobs', ServiceJobController::class);
    Route::post('service-jobs_update/{id}', [ServiceJobController::class, 'update'])->name('service-jobs_update');

  
Route::post('service-jobs/{id}/restore', [
    ServiceJobController::class,
    'restore'
])->name('service-jobs.restore');

Route::delete('service-jobs/{id}/force-delete', [
    ServiceJobController::class,
    'forceDelete'
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

Route::post('verifications/{id}/restore', [VerificationController::class, 'restore'])
    ->name('verifications.restore');

   

});

 Route::get('/', function () {
    return view('welcome');
});
