<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMatchRequest;
use App\Http\Resources\MatchParticipantResource;
use App\Http\Resources\MatchResource;
use App\Models\MatchModel;
use App\Models\MatchParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $radius = $request->query('radius', 10); // default radius in kilometers

        $query = MatchModel::with(['location', 'participants.user']);

        if ($lat && $lng) {
            $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(locations.latitude)) * cos(radians(locations.longitude) - radians(?)) + sin(radians(?)) * sin(radians(locations.latitude))))";

            // Bindings for the Haversine formula
            $bindings = [$lat, $lng, $lat];

            $query = $query
                ->selectRaw("matches.* , {$haversine} AS distance", $bindings)
                ->join('locations', 'matches.location_id', '=', 'locations.id')
                ->whereNotNull('locations.latitude')
                ->whereNotNull('locations.longitude')
                ->whereRaw("{$haversine} <= ?", array_merge($bindings, [$radius]))
                ->orderBy('distance', 'asc');
        } else {
            $query = $query->latest();
        }

        return MatchResource::collection($query->paginate(15));
    }

    public function store(StoreMatchRequest $request)
    {
        $match = MatchModel::create([
            'creator_id' => Auth::id(),
            ...$request->validated(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match created successfully',
            'data' => new MatchResource($match->load(['location', 'participants.user'])),
        ], 201);
    }

    public function show(MatchModel $match)
    {
        return new MatchResource($match->load(['location', 'participants.user']));
    }

    public function join(Request $request, MatchModel $match)
    {
        $user = Auth::user();

        if ($match->creator_id === $user->id) {
            return response()->json(['message' => 'You cannot join your own match'], 422);
        }

        if ($match->status === 'full' || $match->slots_available <= 0) {
            return response()->json(['message' => 'This match is full'], 422);
        }

        $existing = $match->participants()->where('user_id', $user->id)->first();

        if ($existing) {
            if ($existing->status === 'joined') {
                return response()->json(['message' => 'You already joined this match'], 422);
            }

            $existing->update(['status' => 'joined']);

            $match->decrement('slots_available');

            if ($match->slots_available <= 0) {
                $match->update(['status' => 'full']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Joined match successfully',
                'data' => new MatchParticipantResource($existing->fresh()->load('user')),
            ]);
        }

        $participant = MatchParticipant::create([
            'match_id' => $match->id,
            'user_id' => $user->id,
            'status' => 'joined',
        ]);

        $match->decrement('slots_available');

        if ($match->slots_available <= 0) {
            $match->update(['status' => 'full']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Joined match successfully',
            'data' => new MatchParticipantResource($participant->load('user')),
        ]);
    }

    public function leave(Request $request, MatchModel $match)
    {
        $user = Auth::user();

        $participant = $match->participants()->where('user_id', $user->id)->first();

        if (! $participant) {
            return response()->json(['message' => 'You are not a participant in this match'], 404);
        }

        $participant->delete();

        if ($match->status === 'full') {
            $match->update(['status' => 'open']);
        }

        $match->increment('slots_available');

        return response()->json([
            'success' => true,
            'message' => 'Left match successfully',
        ]);
    }
}
