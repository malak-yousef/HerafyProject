<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CraftmanController;
use App\Http\Controllers\ProfessionController;
//use App\Models\Category;
use Illuminate\Support\Facades\Route;


Route::prefix('cms/admin')->group(function () {
    //    Route::view('home' , 'cms.parent');
    Route::view('temp', 'cms.temp')->name('main');
    Route::resource('craftmen', CraftmanController::class);
    Route::post('craftmen_update/{id}', [CraftmanController::class, 'update'])->name('craftmen_update');

    Route::resource('categories' , CategoryController::class);
   Route::post('categories_update/{id}' , [CategoryController::class ,'update'])->name('categories_update');
   Route::get('categories_trashed' , [CategoryController::class ,'trashed'])->name('categories_trashed');
   Route::get('categories_restore/{id}' , [CategoryController::class ,'restore'])->name('categories_restore');
   Route::get('categories_force/{id}' , [CategoryController::class ,'force'])->name('categories_force');
   Route::get('categories_forceAll' , [CategoryController::class ,'forceAll'])->name('categories_forceAll');

   Route::resource('professions' , ProfessionController::class);
   Route::post('professions_update/{id}' , [ProfessionController::class ,'update'])->name('professions_update');
   Route::get('professions_trashed' , [ProfessionController::class ,'trashed'])->name('professions_trashed');
   Route::get('professions_restore/{id}' , [ProfessionController::class ,'restore'])->name('professions_restore');
   Route::get('professions_force/{id}' , [ProfessionController::class ,'force'])->name('professions_force');
   Route::get('professions_forceAll' , [ProfessionController::class ,'forceAll'])->name('professions_forceAll');

   Route::resource('admins' , AdminController::class);
   Route::post('admins_update/{id}' , [AdminController::class ,'update'])->name('admins_update');
   Route::get('admins_trashed' , [AdminController::class ,'trashed'])->name('admins_trashed');
   Route::get('admins_restore/{id}' , [AdminController::class ,'restore'])->name('admins_restore');
   Route::get('admins_force/{id}' , [AdminController::class ,'force'])->name('admins_force');
   Route::get('admins_forceAll' , [AdminController::class ,'forceAll'])->name('admins_forceAll');
   
});



Route::get('/', function () {
    return view('welcome');
});