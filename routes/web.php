<?php

use App\Http\Controllers\CraftmanController;
use Illuminate\Support\Facades\Route;


Route::prefix('cms/admin')->group(function(){
//    Route::view('home' , 'cms.parent');
   Route::view('temp' , 'cms.temp');
   Route::resource('craftmen' , CraftmanController::class);
});



Route::get('/', function () {
    return view('welcome');
});