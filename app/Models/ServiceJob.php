<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Report;
class ServiceJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_jobs';

    protected $fillable = [
        'order_id',
        'offer_id',
        'customer_id',
        'craftman_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function craftman()
    {
        return $this->belongsTo(Craftman::class, 'craftman_id');
    }
    public function reports()
{
    return $this->hasMany(Report::class, 'job_id');
}
}