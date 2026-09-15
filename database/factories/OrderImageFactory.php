<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'image_path' => '1786050282_children.png', // استخدام إحدى الصور الظاهرة بالشجرة
        ];
    }
}
