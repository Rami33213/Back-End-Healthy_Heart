<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeartRateRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method',
        'video_path',
        'heart_rate_value',
        'confidence',
        'processing_time',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'confidence' => 'float',
        'processing_time' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'video_path', // إخفاء المسار الداخلي
    ];

    protected $appends = [
        'video_url',
        'status',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على رابط الفيديو
     */
    public function getVideoUrlAttribute()
    {
        return $this->video_path 
            ? asset('storage/' . $this->video_path) 
            : null;
    }

    /**
     * الحصول على حالة ضربات القلب
     */
    public function getStatusAttribute()
    {
        $hr = $this->heart_rate_value;

        if ($hr < 60) return 'low';
        if ($hr > 100) return 'high';
        return 'normal';
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'low':
                return '#FFA726'; // orange
            case 'high':
                return '#EF5350'; // red
            default:
                return '#66BB6A'; // green
        }
    }
}