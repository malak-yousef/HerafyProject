<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema; // 1. أضف هذه المكتبة
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    
    public function boot(): void
    {
        Paginator::useBootstrap();

        // 2. التحقق من وجود الاتصال وقاعدة البيانات وجدول orders لتجنب الخطأ أثناء الـ Migration
        try {
            if (Schema::hasTable('orders')) {
                View::share('ordersCount', Order::count());
            } else {
                View::share('ordersCount', 0);
            }
        } catch (\Exception $e) {
            // في حال لم يتم إعداد قاعدة البيانات بعد
            View::share('ordersCount', 0);
        }
    }
        
}
