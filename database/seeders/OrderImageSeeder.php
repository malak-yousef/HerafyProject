<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderImage;
use App\Models\Order;


class OrderImageSeeder extends Seeder
{

    public function run(): void
    {

        Order::all()->each(function($order){

            OrderImage::factory()
                ->count(3)
                ->create([
                    'order_id'=>$order->id
                ]);

        });

    }
}
