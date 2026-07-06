<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Craftman;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioFactory> */
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'title',
        'description',
        'location',
        'status',
        'portfolioCover_image',
        'craftman_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function craftman()
    {
        return $this->belongsTo(Craftman::class);
    }
}
