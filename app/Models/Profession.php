<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{Craftman, Category};

class Profession extends Model
{
    /** @use HasFactory<\Database\Factories\ProfessionFactory> */
    use HasFactory, SoftDeletes;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function craftsmen()
    {
        return $this->hasMany(Craftman::class);
    }


    protected $fillable = [
        'name',
        'description',
        'status',
        'category_id',
    ];
}
