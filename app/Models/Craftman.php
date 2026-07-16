<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{
    Portfolio,
    User,
};
use Illuminate\Database\Eloquent\SoftDeletes;

class Craftman extends Model
{
    /** @use HasFactory<\Database\Factories\CraftmanFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'craft_name',
        'id_number',
        'experience_years',
        'description',
        'hourly_rate',
        'daily_rate',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
    ];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function user()
    {
        return $this->morphOne(User::class, 'actor', 'actor_type', 'actor_id', 'id');
    }
}
