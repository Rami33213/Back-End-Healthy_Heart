<?php

namespace App\Http\Controllers;

use App\Models\HeartRateRecord;
use Illuminate\Http\Request;
use App\Helpers\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HeartRateRecordController extends Controller
{
    /**
     * عرض جميع قياسات المستخدم
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $records = $user->heartRateRecords()
                ->orderBy('created_at', 'desc')
                ->get();

            AuditLogger::log('Viewed heart rate record list');

            return response()->json([
                'success' => true,
                'data' => $records
            ], 200);
        } catch (\Exception $e) {
            \Log::error('HeartRate Index Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * حفظ قياس جديد
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'method' => 'required|in:camera,upload',
                'video' => 'required|file|mimes:mp4,avi,mov,webm|max:51200',
            ]);

            // حفظ الفيديو
            $video = $request->file('video');
            $fileName = Str::uuid() . '.' . $video->getClientOriginalExtension();
            $videoPath = $video->storeAs('heart_rate_videos', $fileName, 'public');
            $fullPath = storage_path('app/public/' . $videoPath);

            // استدعاء FastAPI
            $mlApiUrl = env('ML_HEART_RATE_API_URL', 'http://127.0.0.1:8003');
            $endpoint = $validated['method'] === 'camera'
                ? '/analyze-camera'
                : '/analyze-video';

            try {
                $response = Http::timeout(120)
                    ->attach('video', file_get_contents($fullPath), $fileName)
                    ->post($mlApiUrl . $endpoint, [
                        'user_id' => $user->id,
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('ML API request failed: ' . $response->body());
                }

                $mlResult = $response->json();

                if (!$mlResult['success']) {
                    throw new \Exception($mlResult['message'] ?? 'ML processing failed');
                }

                // حفظ النتيجة
                $record = HeartRateRecord::create([
                    'user_id' => $user->id,
                    'method' => $validated['method'],
                    'video_path' => $videoPath,
                    'heart_rate_value' => (int) round($mlResult['heart_rate']),
                    'confidence' => $mlResult['confidence'] ?? null,
                    'processing_time' => $mlResult['processing_time'] ?? null,
                    'metadata' => $mlResult['metadata'] ?? [],
                ]);

                AuditLogger::log('Created heart rate record: ' . $record->id);

                return response()->json([
                    'success' => true,
                    'message' => 'Heart rate recorded successfully',
                    'record' => [
                        'id' => $record->id,
                        'heart_rate_value' => $record->heart_rate_value,
                        'confidence' => $record->confidence,
                        'method' => $record->method,
                        'created_at' => $record->created_at,
                        'video_url' => Storage::url($record->video_path),
                    ]
                ], 201);
            } catch (\Exception $e) {
                // حذف الفيديو في حالة الخطأ
                if (Storage::disk('public')->exists($videoPath)) {
                    Storage::disk('public')->delete($videoPath);
                }

                \Log::error('FastAPI HeartRate Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'ML service unavailable',
                    'error' => $e->getMessage()
                ], 503);
            }
        } catch (\Exception $e) {
            \Log::error('HeartRate Store Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * عرض آخر قياس
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $record = HeartRateRecord::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'No heart rate record found'
                ], 404);
            }

            AuditLogger::log('Viewed latest heart rate record');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $record->id,
                    'method' => $record->method,
                    'heart_rate_value' => $record->heart_rate_value,
                    'confidence' => $record->confidence,
                    'created_at' => $record->created_at,
                    'video_url' => Storage::url($record->video_path),
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('HeartRate Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * حذف قياس محدد
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $record = HeartRateRecord::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            // حذف الفيديو
            if (Storage::disk('public')->exists($record->video_path)) {
                Storage::disk('public')->delete($record->video_path);
            }

            $record->delete();

            AuditLogger::log('Deleted heart rate record: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('HeartRate Destroy Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
