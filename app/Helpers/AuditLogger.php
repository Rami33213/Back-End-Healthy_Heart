<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * تسجيل نشاط للمستخدم المصادق عليه (من Sanctum أو Web)
     */
    public static function log($action, $description = null): void
    {
        try {
            // محاولة جلب المستخدم من Sanctum أولاً (للـ API)
            $user = Auth::guard('sanctum')->user();

            // إذا ما في مستخدم من Sanctum، نجرب Web Guard (للـ Admin)
            if (!$user) {
                $user = Auth::guard('web')->user();
            }

            // إذا ما في مستخدم أصلاً، نوقف التسجيل
            if (!$user) {
                return;
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // ✅ لا تدع الخطأ يكراش التطبيق
            \Log::error('AuditLogger::log Error: ' . $e->getMessage());
        }
    }

    /**
     * تسجيل نشاط لمستخدم محدد (بدون ما يكون مصادق)
     */
    public static function logFor($userId, $action, $description = null): void
    {
        try {
            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('AuditLogger::logFor Error: ' . $e->getMessage());
        }
    }

    /**
     * تسجيل نشاط بدون ربطه بمستخدم معين
     */
    public static function logAnonymous($action, $description = null): void
    {
        try {
            AuditLog::create([
                'user_id' => null,
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('AuditLogger::logAnonymous Error: ' . $e->getMessage());
        }
    }

    /**
     * تسجيل نشاط Admin (للـ Admin Panel)
     */
    public static function logAdmin($action, $description = null): void
    {
        try {
            $admin = Auth::guard('admin')->user();

            if (!$admin) {
                return;
            }

            AuditLog::create([
                'user_id' => null,
                'action' => "[ADMIN] {$action}",
                'description' => $description ? "Admin: {$admin->name} - {$description}" : "Admin: {$admin->name}",
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            \Log::error('AuditLogger::logAdmin Error: ' . $e->getMessage());
        }
    }
}
