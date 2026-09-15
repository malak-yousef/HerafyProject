<?php

namespace App\Models;

use Database\Factories\PortfolioImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioImage extends Model
{
    /** @use HasFactory<PortfolioImageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image_path',
        'image_name',
        'image_description',
        'portfolio_id',
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
