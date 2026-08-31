<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'title' => $this->title,
            'description' => $this->description,
            'location_id' => $this->location_id,
            'location' => $this->whenLoaded('location', function () {
                return [
                    'id' => $this->location->id,
                    'name' => $this->location->name,
                    'city' => $this->location->city,
                    'district' => $this->location->district,
                ];
            }),
            'skill_level' => $this->skill_level,
            'match_type' => $this->match_type,
            'slots_available' => $this->slots_available,
            'match_date' => $this->match_date,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'participants' => MatchParticipantResource::collection($this->whenLoaded('participants') ? $this->participants : $this->participants()->with('user')->get()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'distance' => isset($this->distance) ? round($this->distance, 2) : null,
        ];
    }
}
