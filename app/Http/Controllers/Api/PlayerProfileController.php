<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlayerProfile;
use App\Http\Requests\StorePlayerProfileRequest;
use App\Http\Requests\UpdatePlayerProfileRequest;
use App\Http\Resources\PlayerProfileResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlayerProfileController extends Controller
{
    /**
     * Create a new player profile
     * POST /api/profile
     */
    public function create(StorePlayerProfileRequest $request)
    {
        // Check if profile already exists
        $existing = PlayerProfile::where('user_id', Auth::id())->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Player profile already exists'
            ], 409);
        }

        $profile = PlayerProfile::create([
            'user_id' => Auth::id(),
            ...$request->validated()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully',
            'data' => new PlayerProfileResource($profile)
        ], 201);
    }

    /**
     * Get authenticated user's profile
     * GET /api/profile
     */
    public function show()
    {
        $profile = PlayerProfile::where('user_id', Auth::id())->firstOrFail();
        return new PlayerProfileResource($profile);
    }

    /**
     * Update authenticated user's profile
     * PUT /api/profile
     */
    public function update(UpdatePlayerProfileRequest $request)
    {
        $profile = PlayerProfile::where('user_id', Auth::id())->firstOrFail();
        
        $profile->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new PlayerProfileResource($profile)
        ]);
    }

    /**
     * Get any player's public profile
     * GET /api/players/{id}
     */
    public function showPublic($id)
    {
        Log::info('Fetching public profile for user ID: ' . $id);
        $profile = PlayerProfile::where('user_id', $id)->firstOrFail();
        return new PlayerProfileResource($profile);
    }
}
