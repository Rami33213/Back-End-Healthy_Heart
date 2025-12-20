<?php

// app/Models/ExpertConsultation.php

// app/Models/ExpertConsultation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symptoms',        // JSON لكل الإجابات نعم/لا
        'recommendation',  // نص التوصية
        'diagnosis_label', // تشخيص نهائي
        'risk_level',      // high / medium / low
        'risk_score',      // قيمة رقمية 0-1
    ];

    protected $casts = [
        'symptoms' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
