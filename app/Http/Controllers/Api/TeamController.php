<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamMemberResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        return TeamResource::collection(Team::with('owner', 'members.user')->paginate(15));
    }

    public function store(StoreTeamRequest $request)
    {
        $team = Team::create(array_merge($request->validated(), ['owner_id' => Auth::id()]));

        return new TeamResource($team->load('owner', 'members.user'));
    }

    public function show(Team $team)
    {
        return new TeamResource($team->load('owner', 'members.user'));
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        if ($team->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $team->update($request->validated());

        return new TeamResource($team->load('owner', 'members.user'));
    }

    public function destroy(Team $team)
    {
        if ($team->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $team->delete();

        return response()->json(['message' => 'Team deleted']);
    }

    public function members(Team $team)
    {
        return TeamMemberResource::collection($team->members()->with('user')->get());
    }

    public function addMember(Request $request, Team $team)
    {
        if ($team->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string',
            'status' => 'nullable|in:pending,active,rejected',
        ]);

        $member = TeamMember::updateOrCreate(
            ['team_id' => $team->id, 'user_id' => $validated['user_id']],
            [
                'role' => $validated['role'] ?? 'member',
                'status' => $validated['status'] ?? 'active',
                'joined_at' => now(),
            ]
        );

        return new TeamMemberResource($member->load('user'));
    }

    public function removeMember(Team $team, TeamMember $member)
    {
        if ($team->id !== $member->team_id) {
            return response()->json(['message' => 'Member does not belong to this team'], 400);
        }

        if ($team->owner_id !== Auth::id() && $member->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $member->delete();

        return response()->json(['message' => 'Member removed']);
    }
}
