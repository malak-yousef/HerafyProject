<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'profession_id',
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

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function scopeEligibleForCraftman(Builder $query, Craftman $craftman): Builder
    {
        return $query
            ->where('status', 'pending')
            ->where('profession_id', $craftman->profession_id);
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

    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function serviceJob()
    {
        return $this->hasOne(ServiceJob::class);
    }
}
