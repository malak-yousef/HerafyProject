<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Admin extends Model
{
    use HasFactory;

    public function user(){
        return $this->morphOne(User::class , 'actor' , 'actor_type' , 'actor_id' , 'id');
    }

    protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'mobile',
    'address',
    'date',
    'image',
    'password',
    'status',
    'gender',
];
protected $hidden = [
    'password',
    'remember_token',
];

}
