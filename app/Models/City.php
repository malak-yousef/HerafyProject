<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Area;


class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
