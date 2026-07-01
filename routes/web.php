<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CraftmanController;
use App\Http\Controllers\ProfessionController;
//use App\Models\Category;
use Illuminate\Support\Facades\Route;


Route::prefix('cms/admin')->group(function(){
//    Route::view('home' , 'cms.parent');
   Route::view('temp' , 'cms.temp');
   Route::resource('craftmen' , CraftmanController::class);
   Route::resource('categories' , CategoryController::class);
   Route::post('categories_update/{id}' , [CategoryController::class ,'update'])->name('categories_update');
   Route::resource('professions' , ProfessionController::class);
   Route::post('professions_update/{id}' , [ProfessionController::class ,'update'])->name('professions_update');
});



Route::get('/', function () {
    return view('welcome');
});