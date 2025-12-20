<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ECGRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'result',
        'confidence_score'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diagnosis()
    {
        return $this->hasOne(Diagnosis::class, 'source_id')->where('source_type', 'ECGRecord');
    }
}

