<?php

namespace App\Http\Controllers;

use App\Models\MedicalTest;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client;

class MedicalTestController extends Controller
{
    /**
     * تخزين التحليل + استدعاء FastAPI
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'blood_sugar' => 'required|numeric',
                'ck_mb'       => 'required|numeric',
                'troponin'    => 'required|numeric',
            ]);

            // جلب بيانات البروفايل
            $profile = $user->profile;

            if (!$profile || !$profile->date_of_birth || !$profile->gender) {
                return response()->json([
                    'message' => 'Incomplete profile data (date_of_birth & gender are required)',
                ], 422);
            }

            $age    = Carbon::parse($profile->date_of_birth)->age;
            $gender = $profile->gender;

            // حفظ التحليل
            $test = MedicalTest::create([
                'user_id'     => $user->id,
                'blood_sugar' => $validated['blood_sugar'],
                'ck_mb'       => $validated['ck_mb'],
                'troponin'    => $validated['troponin'],
            ]);

            // استدعاء FastAPI
            try {
                $client = new Client([
                    'base_uri' => config('services.ml_api.url'),
                    'timeout'  => 5.0,
                ]);

                $response = $client->post('/predict', [
                    'json' => [
                        'age'         => $age,
                        'gender'      => $gender,
                        'blood_sugar' => $validated['blood_sugar'],
                        'ck_mb'       => $validated['ck_mb'],
                        'troponin'    => $validated['troponin'],
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);

                $predictionLabel   = $body['prediction_label'] ?? 'unknown';
                $positiveProba     = $body['positive_probability'] ?? null;
                $confidence        = $positiveProba !== null ? (float) $positiveProba : null;

                // إنشاء تشخيص
                $diagnosis = Diagnosis::create([
                    'user_id'          => $user->id,
                    'source_type'      => 'MedicalTest',
                    'source_id'        => $test->id,
                    'result'           => $predictionLabel,
                    'confidence_score' => $confidence,
                ]);

                $test->setRelation('diagnosis', $diagnosis);
            } catch (\Exception $e) {
                \Log::error('FastAPI MedicalTest Error: ' . $e->getMessage());

                AuditLogger::log('Medical test saved but ML service failed');

                return response()->json([
                    'message'  => 'Medical test saved, but AI prediction failed',
                    'test'     => $test,
                    'age'      => $age,
                    'gender'   => $gender,
                    'ml_error' => 'ML service unavailable',
                ], 201);
            }

            AuditLogger::log('Created medical test with AI diagnosis');

            return response()->json([
                'message'   => 'Medical test saved and analyzed successfully',
                'test'      => $test,
                'age'       => $age,
                'gender'    => $gender,
                'diagnosis' => $test->diagnosis,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('MedicalTest Store Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * آخر تحليل
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $test = MedicalTest::with('diagnosis')
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$test) {
                return response()->json(['message' => 'No medical test found'], 404);
            }

            $profile = $user->profile;
            $age     = null;
            $gender  = null;

            if ($profile && $profile->date_of_birth && $profile->gender) {
                $age    = Carbon::parse($profile->date_of_birth)->age;
                $gender = $profile->gender;
            }

            AuditLogger::log('Viewed latest medical test with diagnosis');

            return response()->json([
                'id'          => $test->id,
                'user_id'     => $test->user_id,
                'blood_sugar' => $test->blood_sugar,
                'ck_mb'       => $test->ck_mb,
                'troponin'    => $test->troponin,
                'age'         => $age,
                'gender'      => $gender,
                'diagnosis'   => $test->diagnosis,
                'created_at'  => $test->created_at,
                'updated_at'  => $test->updated_at,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('MedicalTest Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
