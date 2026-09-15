<?php

namespace App\Models;

use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'order_id',
        'craftman_id',
        'price',
        'message',
        'status',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع الحرفي
     */
    public function craftman()
    {
        return $this->belongsTo(Craftman::class);
    }

    public function serviceJob()
    {
        return $this->hasOne(ServiceJob::class);
    }
}
