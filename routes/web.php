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

    AreaController,
    CityController,
    OrderController,

};

//use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin,craftman,customer,web')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:admin,craftman,customer');

Route::prefix('cms/admin')->middleware('auth:admin,craftman,customer')->group(function () {
    Route::view('', 'cms.parent');
    Route::view('temp', 'cms.temp')->name('main');

    Route::resource('craftmen', CraftmanController::class);
    Route::post('craftmen_update/{id}', [CraftmanController::class, 'update'])->name('craftmen_update');
    Route::get('craftmen_trashed', [CraftmanController::class, 'trashed'])->name('craftmen_trashed');
    Route::get('craftmen_restore/{id}', [CraftmanController::class, 'restore'])->name('craftmen_restore');
    Route::get('craftmen_force_destroy/{id}', [CraftmanController::class, 'forceDestroy'])->name('craftmen_force_destroy');
    Route::get('craftmen_destroy_all', [CraftmanController::class, 'DestroyAll'])->name('craftmen_destroy_all');

    Route::resource('categories', CategoryController::class);
    Route::post('categories_update/{id}', [CategoryController::class, 'update'])->name('categories_update');
    Route::get('categories_trashed', [CategoryController::class, 'trashed'])->name('categories_trashed');
    Route::get('categories_restore/{id}', [CategoryController::class, 'restore'])->name('categories_restore');
    Route::get('categories_force/{id}', [CategoryController::class, 'force'])->name('categories_force');
    Route::get('categories_forceAll', [CategoryController::class, 'forceAll'])->name('categories_forceAll');

    Route::resource('professions', ProfessionController::class);
    Route::post('professions_update/{id}', [ProfessionController::class, 'update'])->name('professions_update');
    Route::get('professions_trashed', [ProfessionController::class, 'trashed'])->name('professions_trashed');
    Route::get('professions_restore/{id}', [ProfessionController::class, 'restore'])->name('professions_restore');
    Route::get('professions_force/{id}', [ProfessionController::class, 'force'])->name('professions_force');
    Route::get('professions_forceAll', [ProfessionController::class, 'forceAll'])->name('professions_forceAll');

    Route::resource('admins', AdminController::class);
    Route::post('admins_update/{id}', [AdminController::class, 'update'])->name('admins_update');
    Route::get('admins_trashed', [AdminController::class, 'trashed'])->name('admins_trashed');
    Route::get('admins_restore/{id}', [AdminController::class, 'restore'])->name('admins_restore');
    Route::get('admins_force/{id}', [AdminController::class, 'force'])->name('admins_force');
    Route::get('admins_forceAll', [AdminController::class, 'forceAll'])->name('admins_forceAll');


    Route::resource('portfolios', PortfolioController::class);
    Route::post('portfolios_update/{id}', [PortfolioController::class, 'update'])->name('portfolios_update');
    Route::get('portfolios_trashed', [PortfolioController::class, 'trashed'])->name('portfolios_trashed');
    Route::get('portfolios_restore/{id}', [PortfolioController::class, 'restore'])->name('portfolios_restore');
    Route::get('portfolios_force_destroy/{id}', [PortfolioController::class, 'forceDestroy'])->name('portfolios_force_destroy');
    Route::get('portfolios_destroy_all', [PortfolioController::class, 'DestroyAll'])->name('portfolios_destroy_all');

    Route::resource('portfolio_images', PortfolioImageController::class);
    Route::post('portfolio_images_update/{id}', [PortfolioImageController::class, 'update'])->name('portfolio_images_update');
    Route::get('portfolio_images_trashed', [PortfolioImageController::class, 'trashed'])->name('portfolio_images_trashed');
    Route::get('portfolio_images_restore/{id}', [PortfolioImageController::class, 'restore'])->name('portfolio_images_restore');
    Route::get('portfolio_images_force_destroy/{id}', [PortfolioImageController::class, 'forceDestroy'])->name('portfolio_images_force_destroy');
    Route::get('portfolio_images_destroy_all', [PortfolioImageController::class, 'DestroyAll'])->name('portfolio_images_destroy_all');

    Route::resource('customers', CustomerController::class);
    Route::post('customers_update/{id}', [CustomerController::class, 'update'])->name('customers_update');
    Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])
        ->name('customers.orders');
    Route::get('customers_trashed', [CustomerController::class, 'trashed'])->name('customers_trashed');
    Route::get('customers_restore/{id}', [CustomerController::class, 'restore'])->name('customers_restore');
    Route::get('customers_force/{id}', [CustomerController::class, 'force'])->name('customers_force');
    Route::get('customers_forceAll', [CustomerController::class, 'forceAll'])->name('customers_forceAll');


    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('roles/{id}/permissions', [RoleController::class, 'editRolePermissions'])->name('roles.edit-permissions');
    Route::post('roles/{id}/permissions', [RoleController::class, 'updateRolePermissions'])->name('roles.update-permissions');

    Route::resource('areas', AreaController::class);
    Route::post('areas_update/{id}', [AreaController::class, 'update'])->name('areas_update');
    Route::get('areas_trashed', [AreaController::class, 'trashed'])->name('areas_trashed');
    Route::get('areas_restore/{id}', [AreaController::class, 'restore'])->name('areas_restore');
    Route::get('areas_force/{id}', [AreaController::class, 'force'])->name('areas_force');
    Route::get('areas_forceAll', [AreaController::class, 'forceAll'])->name('areas_forceAll');

    Route::resource('cities', CityController::class);
    Route::post('cities_update/{id}', [CityController::class, 'update'])->name('cities_update');
    Route::get('cities_trashed', [CityController::class, 'trashed'])->name('cities_trashed');
    Route::get('cities_restore/{id}', [CityController::class, 'restore'])->name('cities_restore');
    Route::get('cities_force/{id}', [CityController::class, 'force'])->name('cities_force');
    Route::get('cities__forceAll', [CityController::class, 'forceAll'])->name('cities_forceAll');

    Route::resource('orders', OrderController::class);
    Route::post('orders_update/{id}', [OrderController::class, 'update'])->name('orders_update');
    Route::get('orders_trashed', [OrderController::class, 'trashed'])->name('orders_trashed');
    Route::get('orders_restore/{id}', [OrderController::class, 'restore'])->name('orders_restore');
    Route::get('orders_force/{id}', [OrderController::class, 'force'])->name('orders_force');
    Route::get('orders_forceAll', [OrderController::class, 'forceAll'])->name('orders_forceAll');

});



Route::get('/', function () {
    return view('welcome');
});
