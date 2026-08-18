<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class OrderImageFactory extends Factory
{
    public function definition(): array
{
    return [
        'order_id'   => Order::factory(),
        'image_path' => '1786050282_children.png', // استخدام إحدى الصور الظاهرة بالشجرة
    ];
}
}