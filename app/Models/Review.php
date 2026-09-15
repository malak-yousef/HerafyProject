<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rating',
        'comment',
        'craftsman_id',
        'customer_id',
        'request_id',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function craftsman()
    {
        return $this->belongsTo(Craftman::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function request()
    {
        return $this->belongsTo(Order::class, 'request_id');
    }
}
