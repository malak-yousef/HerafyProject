<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\City;
use App\Models\Order;

use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'city_id',
        'name',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
