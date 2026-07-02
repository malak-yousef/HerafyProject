<?php

use App\Http\Controllers\CraftmanController;
use Illuminate\Support\Facades\Route;


Route::prefix('cms/admin')->group(function () {
    //    Route::view('home' , 'cms.parent');
    Route::view('temp', 'cms.temp')->name('main');
    Route::resource('craftmen', CraftmanController::class);
    Route::post('craftmen_update/{id}', [CraftmanController::class, 'update'])->name('craftmen_update');
});



Route::get('/', function () {
    return view('welcome');
});