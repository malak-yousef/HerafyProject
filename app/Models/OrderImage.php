<?php

namespace App\Models;

use Database\Factories\OrderImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderImage extends Model
{
    /** @use HasFactory<OrderImageFactory> */
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'order_id',
        'image_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function publicUrl(): string
    {
        $path = str_replace('\\', '/', ltrim($this->image_path, '/'));

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (! str_contains($path, '/')) {
            $path = 'images/order_images/'.$path;
        }

        $baseUrl = rtrim(request()->getBaseUrl(), '/');

        return $baseUrl.'/storage/'.$path;
    }
}
