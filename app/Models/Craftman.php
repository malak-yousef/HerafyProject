<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\SoftDeletes;

class Craftman extends Model
{
    /** @use HasFactory<\Database\Factories\CraftmanFactory> */
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'name',
        'craft_name',
        'email',
        'password',
        'phone',
        'profile_image',
        'experience_years',
        'description',
        'rating',
        'total_reviews',
        'is_active',
        'hourly_rate',
        'daily_rate',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function portfolios() {
        return $this->hasMany(Portfolio::class);
    } 
}