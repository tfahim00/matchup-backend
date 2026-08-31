<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\MatchModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NearbySearchSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->create([
                'name' => 'Seed User',
                'email' => 'seed@example.com',
            ]);
        }

        // London location (central)
        $london = Location::create([
            'name' => 'London Central Pitch',
            'address' => 'Some address, London',
            'city' => 'London',
            'district' => 'Central',
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        // Paris location (far)
        $paris = Location::create([
            'name' => 'Paris Pitch',
            'address' => 'Some address, Paris',
            'city' => 'Paris',
            'district' => 'Central',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        // Nearby match in London
        MatchModel::create([
            'creator_id' => $user->id,
            'home_team_id' => null,
            'away_team_id' => null,
            'title' => 'London 7v7 Friendly',
            'description' => 'Casual 7-a-side in London',
            'location_id' => $london->id,
            'skill_level' => 'mixed',
            'match_type' => '7v7',
            'slots_available' => 14,
            'match_date' => Carbon::now()->addDays(3),
            'status' => 'open',
            'visibility' => 'public',
        ]);

        // Far match in Paris
        MatchModel::create([
            'creator_id' => $user->id,
            'home_team_id' => null,
            'away_team_id' => null,
            'title' => 'Paris 11v11 League',
            'description' => 'Competitive 11-a-side in Paris',
            'location_id' => $paris->id,
            'skill_level' => 'advanced',
            'match_type' => '11v11',
            'slots_available' => 22,
            'match_date' => Carbon::now()->addDays(7),
            'status' => 'open',
            'visibility' => 'public',
        ]);
    }
}
