<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;
use App\Models\ServiceJob;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_number',

    ];
    protected $casts = [
        'id_number' => 'string',
    ];
    public function user()
    {
        return $this->morphOne(User::class, 'actor', 'actor_type', 'actor_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function serviceJobs()
    {
        return $this->hasMany(ServiceJob::class, 'customer_id');
    }
}
