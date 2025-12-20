<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable=['address',
    'date_of_birth',
    'gender',
    'user_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
    
}
