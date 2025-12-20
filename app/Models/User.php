<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    // 👇 أضف هذا الكود للحذف التلقائي
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // حذف كل السجلات المرتبطة قبل حذف المستخدم
            $user->profile()->delete();
            $user->medicalTests()->delete();
            $user->ecgRecords()->delete();
            $user->heartRateRecords()->delete();
            $user->diagnosis()->delete();
            $user->expertConsultations()->delete();
            $user->reports()->delete();
            $user->settings()->delete();
            $user->auditLogs()->delete();
        });
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    
    public function medicalTests()
    {
        return $this->hasMany(MedicalTest::class);
    }
    
    public function ecgRecords()
    {
        return $this->hasMany(ECGRecord::class);
    }

    public function heartRateRecords()
    {
        return $this->hasMany(HeartRateRecord::class);
    }

    public function diagnosis()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function expertConsultations()
    {
        return $this->hasMany(ExpertConsultation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    public function settings()
    {
        return $this->hasOne(Settings::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            //'email_verified_at' => 'datetime',
            //'password' => 'hashed',
        ];
    }
}
