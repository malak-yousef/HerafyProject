<?php

namespace App\Models;

use Database\Factories\CraftmanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Craftman extends Model
{
    /** @use HasFactory<CraftmanFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'craftmen';

    protected $fillable = [
        // 'craft_name',
        'id_number',
        'experience_years',
        'description',
        'hourly_rate',
        'daily_rate',
        'profession_id',
        'is_featured',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function user()
    {
        return $this->morphOne(User::class, 'actor', 'actor_type', 'actor_id', 'id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function serviceJobs()
    {
        return $this->hasMany(ServiceJob::class, 'craftman_id');
    }

    public function scopeEligibleForOrderNotification(Builder $query, Order $order): Builder
    {
        return $query
            ->where('profession_id', $order->profession_id)
            ->whereHas('user', fn (Builder $query) => $query->where('status', 'active'));
    }

    public function scopePubliclyDiscoverable(Builder $query): Builder
    {
        return $query->whereHas(
            'user',
            fn (Builder $query) => $query->where('status', 'active')
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'craftsman_id');
    }

    public function customerReviews()
    {
        return $this->hasMany(CustomerReview::class);
    }

    public function verifications(): HasMany
    {

        // protected $fillable = [
        //     'craft_name',
        //     'id_number',
        //     'experience_years',
        //     'description',
        //     'hourly_rate',
        //     'daily_rate',
        //     'profession_id',
        // ];

        // protected $casts = [
        //     'hourly_rate' => 'decimal:2',
        //     'daily_rate' => 'decimal:2',
        // ];

        // public function portfolios()
        // {
        //     return $this->hasMany(Portfolio::class);
        // }
        // public function profession()
        // {
        //     return $this->belongsTo(Profession::class);
        // }
        // public function user()
        // {
        //     return $this->morphOne(User::class, 'actor', 'actor_type', 'actor_id', 'id');
        // }

        // public function reviews()
        // {
        //     return $this->hasMany(Review::class);
        // }

        return $this->hasMany(Verification::class);
    }
}
