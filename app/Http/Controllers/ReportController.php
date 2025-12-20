<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $reports = $user->reports;

            AuditLogger::log('Viewed report list');

            return response()->json($reports, 200);
        } catch (\Exception $e) {
            \Log::error('Report Index Error: ' . $e->getMessage());
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
                'record_type' => 'required|in:HeartRateRecord,ECGRecord,MedicalTest,Diagnosis',
                'record_id' => 'required|integer'
            ]);

            $report = Report::create([
                'user_id' => $user->id,
                'record_type' => $validated['record_type'],
                'record_id' => $validated['record_id']
            ]);

            AuditLogger::log('Created report');

            return response()->json([
                'message' => 'Report saved successfully',
                'report' => $report
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Report Store Error: ' . $e->getMessage());
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

            $report = Report::with('record')
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            if (!$report) {
                return response()->json(['message' => 'No report found'], 404);
            }

            AuditLogger::log('Viewed latest report');

            return response()->json([
                'id' => $report->id,
                'record_type' => $report->record_type,
                'record_id' => $report->record_id,
                'record' => $report->record,
                'created_at' => $report->created_at
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Report Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
