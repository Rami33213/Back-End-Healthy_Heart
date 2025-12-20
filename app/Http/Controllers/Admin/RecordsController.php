<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ECGRecord;
use App\Models\HeartRateRecord;
use App\Models\MedicalTest;
use App\Models\ExpertConsultation;  
// ✅ تأكد من هذا السطر
use App\Models\Diagnosis;

class RecordsController extends Controller
{
    public function ecg()
    {
        $records = ECGRecord::with('user', 'diagnosis')
            ->latest()
            ->paginate(20);
            
        return view('admin.records.ecg', compact('records'));
    }

    public function heartRate()
    {
        $records = HeartRateRecord::with('user')
            ->latest()
            ->paginate(20);
            
        return view('admin.records.heart-rate', compact('records'));
    }

    public function medicalTests()
    {
        $records = MedicalTest::with('user', 'diagnosis')
            ->latest()
            ->paginate(20);
            
        return view('admin.records.medical-tests', compact('records'));
    }

    public function consultations()
    {
        $records = ExpertConsultation::with('user')
            ->latest()
            ->paginate(20);
            
        return view('admin.records.consultations', compact('records'));
    }

    public function diagnosis()
    {
        $records = Diagnosis::with('user', 'source')
            ->latest()
            ->paginate(20);
            
        return view('admin.records.diagnosis', compact('records'));
    }
}
