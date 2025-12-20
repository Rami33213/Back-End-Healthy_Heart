<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['user_id', 'settings_json'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
