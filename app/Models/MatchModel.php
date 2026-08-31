<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = ['creator_id','home_team_id','away_team_id','title','description','location_id','skill_level','match_type','slots_available','match_date','status','visibility'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function participants()
    {
        return $this->hasMany(MatchParticipant::class, 'match_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'match_participants', 'match_id', 'user_id')->withTimestamps();
    }
}
