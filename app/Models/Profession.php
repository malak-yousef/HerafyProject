<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    /** @use HasFactory<\Database\Factories\ProfessionFactory> */
    use HasFactory;
     public function category(){
        return $this->belongsTo(Category::class);
    }

    protected $fillable = [
    'name',
    'description',
    'status',
    'category_id',
];
}
