<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bio' => $this->bio,
            'preferred_position' => $this->preferred_position,
            'skill_level' => $this->skill_level,
            'age' => $this->age,
            'height' => $this->height,
            'weight' => $this->weight,
            'dominant_foot' => $this->dominant_foot,
            'city' => $this->city,
            'district' => $this->district,
            'rating' => $this->rating,
            'matches_played' => $this->matches_played,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
