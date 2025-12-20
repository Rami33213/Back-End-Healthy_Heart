<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $settings = $user->settings;

            if (!$settings) {
                return response()->json(['message' => 'No settings found'], 404);
            }

            return response()->json(json_decode($settings->settings_json), 200);
        } catch (\Exception $e) {
            \Log::error('Settings Show Error: ' . $e->getMessage());
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
                'settings' => 'required|array'
            ]);

            Settings::updateOrCreate(
                ['user_id' => $user->id],
                ['settings_json' => json_encode($validated['settings'])]
            );

            return response()->json(['message' => 'Settings saved successfully'], 201);
        } catch (\Exception $e) {
            \Log::error('Settings Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
