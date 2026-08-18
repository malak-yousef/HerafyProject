<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ServiceJob;

class offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
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