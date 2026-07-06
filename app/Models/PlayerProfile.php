<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    use HasFactory;

    protected $table = 'player_profiles';

    protected $fillable = [
        'user_id','bio','preferred_position','skill_level','age','height','weight','dominant_foot','city','district','rating','matches_played',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
