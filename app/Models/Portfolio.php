<?php

namespace App\Models;

use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    /** @use HasFactory<PortfolioFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'status',
        'portfolioCover_image',
        'craftman_id',
        'project_date',
        'views_count',
        'is_featured',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function craftman()
    {
        return $this->belongsTo(Craftman::class);
    }

    public function portfolio_images()
    {
        return $this->hasMany(PortfolioImage::class);
    }
}
