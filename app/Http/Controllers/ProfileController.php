<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $profile = $user->profile;

            if (!$profile) {
                return response()->json(['message' => 'No profile found'], 404);
            }

            return response()->json($profile, 200);
        } catch (\Exception $e) {
            \Log::error('Profile Index Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function storeOrUpdate(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validated = $request->validate([
                'address' => ['nullable', 'string', 'max:255'],
                'date_of_birth' => ['nullable', 'date'],
                'gender' => ['nullable', 'in:male,female'],
            ]);

            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return response()->json([
                'message' => 'Profile created or updated successfully',
                'profile' => $profile,
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Profile Store Error: ' . $e->getMessage());
            \Log::error('Stack: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
