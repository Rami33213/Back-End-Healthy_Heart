<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'blood_sugar',
        'ck_mb',
        'troponin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class, 'source_id')->where('source_type', 'MedicalTest');
    }
}

