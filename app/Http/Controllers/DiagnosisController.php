<?php

namespace App\Http\Controllers;

use App\Models\MedicalTest;
use App\Models\ECGRecord;
use App\Models\Diagnosis;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Illuminate\Http\JsonResponse;

class DiagnosisController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $diagnosis = $user->diagnosis;

            AuditLogger::log('Viewed diagnosis list');

            return response()->json($diagnosis, 200);
        } catch (\Exception $e) {
            \Log::error('Diagnosis Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'source_type' => 'required|in:MedicalTest,ECGRecord',
                'source_id' => 'required|integer',
                'result' => 'required|string',
                'confidence_score' => 'nullable|numeric'
            ]);

            $diagnosis = Diagnosis::create([
                'user_id' => $user->id,
                ...$validated
            ]);

            AuditLogger::log('Created diagnosis');

            return response()->json([
                'message' => 'Diagnosis saved successfully',
                'diagnosis' => $diagnosis
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Diagnosis Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $diagnosis = Diagnosis::with('source')
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$diagnosis) {
                return response()->json(['message' => 'No diagnosis found'], 404);
            }

            AuditLogger::log('Viewed latest diagnosis');

            return response()->json($diagnosis, 200);
        } catch (\Exception $e) {
            \Log::error('Diagnosis Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
