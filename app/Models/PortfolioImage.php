<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Portfolio;

class PortfolioImage extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioImageFactory> */
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
