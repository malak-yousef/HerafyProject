<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, SoftDeletes;


    public function professions()
    {
        return $this->hasMany(Profession::class);
    }

    protected $fillable = [
        'name',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            $category->professions()->delete();
        });
    }
}
