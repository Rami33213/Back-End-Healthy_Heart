<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ECGRecord;
use App\Models\HeartRateRecord;
use App\Models\MedicalTest;
use App\Models\ExpertConsultation;
use App\Models\Diagnosis;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_ecg' => ECGRecord::count(),
                'total_heart_rate' => HeartRateRecord::count(),
                'total_tests' => MedicalTest::count(),
                'total_consultations' => ExpertConsultation::count(),
                'total_diagnosis' => Diagnosis::count(),
                'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            ];

            // بيانات مبسطة للرسوم
            $userRegistrations = collect([]);
            $diagnosisDistribution = collect([]);
            
            $recentUsers = User::with('profile')->latest()->take(10)->get();
            $recentLogs = AuditLog::with('user')->latest()->take(15)->get();

            return view('admin.dashboard', compact(
                'stats',
                'userRegistrations',
                'diagnosisDistribution',
                'recentUsers',
                'recentLogs'
            ));
            
        } catch (\Exception $e) {
            dd($e->getMessage()); // رح يطبعلك الخطأ على الشاشة
        }
    }
}
