<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderImage;
use Illuminate\Database\Seeder;

class OrderImageSeeder extends Seeder
{
    public function run(): void
    {

        Order::all()->each(function ($order) {

            OrderImage::factory()
                ->count(3)
                ->create([
                    'order_id' => $order->id,
                ]);

        });

    }
}
