<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\City;
use App\Models\Area;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'city_id',
        'area_id',
        'title',
        'description',
        'budget',
        'service_date',
        'status',
        'address',
        'phone',
    ];
    protected $casts = [
        'service_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
