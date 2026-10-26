<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_job_id',
        'craftman_id',
        'customer_id',
        'rating',
        'comment',
    ];

    protected $casts = ['rating' => 'integer'];

    public function serviceJob()
    {
        return $this->belongsTo(ServiceJob::class);
    }

    public function craftman()
    {
        return $this->belongsTo(Craftman::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
