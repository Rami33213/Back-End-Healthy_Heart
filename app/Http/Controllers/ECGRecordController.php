<?php

namespace App\Http\Controllers;

use App\Models\ECGRecord;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ECGRecordController extends Controller
{
    /**
     * قائمة كل سجلات ECG للمستخدم
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $records = ECGRecord::with('diagnosis')
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            AuditLogger::log('Viewed ECG record list');

            return response()->json([
                'success' => true,
                'data'    => $records,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('ECG Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * حفظ ملف ECG + استدعاء FastAPI
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'ecg_image' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $file = $request->file('ecg_image');
            $path = $file->store('ecg_records', 'public');

            // استدعاء FastAPI
            $mlBaseUrl = env('ML_ECG_API_URL', 'http://127.0.0.1:8001');

            try {
                $response = Http::timeout(30)
                    ->asMultipart()
                    ->attach(
                        'file',
                        fopen($file->getRealPath(), 'r'),
                        $file->getClientOriginalName()
                    )
                    ->post($mlBaseUrl . '/predict_ecg');

                if (!$response->successful()) {
                    throw new \Exception('ML service error: ' . $response->body());
                }

                $ml = $response->json();
            } catch (\Exception $e) {
                \Log::error('FastAPI ECG Error: ' . $e->getMessage());
                
                // حفظ السجل بدون ML
                $ecg = ECGRecord::create([
                    'user_id'          => $user->id,
                    'file_path'        => $path,
                    'result'           => 'pending',
                    'confidence_score' => null,
                ]);

                return response()->json([
                    'message' => 'ECG saved but ML service unavailable',
                    'ecg'     => $ecg,
                    'error'   => 'ML service offline',
                ], 201);
            }

            // قراءة نتائج ML
            $predictedClass    = $ml['predicted_class_name'] ?? null;
            $predictedDescAr   = $ml['predicted_class_description_ar'] ?? null;
            $probs             = $ml['probabilities'] ?? [];
            $confidence        = !empty($probs) ? max($probs) : null;

            $result = ($predictedClass === 'Normal') ? 'normal' : 'abnormal';

            $diagnosisResult = $predictedDescAr ?? match ($predictedClass) {
                'MI'     => 'احتشاء عضلة القلب الحاد',
                'PMI'    => 'تاريخ سابق لاحتشاء عضلة القلب',
                'HB'     => 'اضطراب في نظم القلب',
                'Normal' => 'تخطيط قلب ضمن الحدود الطبيعية',
                default  => $result === 'normal'
                    ? 'تخطيط قلب ضمن الحدود الطبيعية'
                    : 'اضطراب في تخطيط القلب',
            };

            // حفظ ECG
            $ecg = ECGRecord::create([
                'user_id'          => $user->id,
                'file_path'        => $path,
                'result'           => $result,
                'confidence_score' => $confidence,
            ]);

            // حفظ Diagnosis
            $diagnosis = Diagnosis::create([
                'user_id'          => $user->id,
                'source_type'      => 'ECGRecord',
                'source_id'        => $ecg->id,
                'result'           => $diagnosisResult,
                'confidence_score' => $confidence,
            ]);

            AuditLogger::log('Created ECG record with ML prediction');

            return response()->json([
                'message'   => 'ECG record analyzed and saved successfully',
                'ecg'       => $ecg,
                'diagnosis' => $diagnosis,
                'ml_raw'    => $ml,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('ECG Store Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * آخر ECG للمستخدم
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $ecg = ECGRecord::with('diagnosis')
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$ecg) {
                return response()->json(['message' => 'No ECG record found'], 404);
            }

            AuditLogger::log('Viewed latest ECG record');

            return response()->json([
                'success' => true,
                'data'    => $ecg,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('ECG Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
