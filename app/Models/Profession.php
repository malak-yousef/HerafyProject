<?php

namespace App\Models;

use Database\Factories\ProfessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profession extends Model
{
    /** @use HasFactory<ProfessionFactory> */
    use HasFactory, SoftDeletes;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function craftsmen()
    {
        return $this->hasMany(Craftman::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected $fillable = [
        'name',
        'description',
        'status',
        'category_id',
    ];
}
