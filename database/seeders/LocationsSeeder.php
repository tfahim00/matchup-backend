<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\MatchModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LocationsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->create([
                'name' => 'Seed User',
                'email' => 'seed2@example.com',
            ]);
        }

        $locations = [
            ['name' => 'Central Park Pitch', 'address' => 'Central Park, Block A', 'city' => 'Dhaka', 'district' => 'Gulshan', 'latitude' => 23.7806, 'longitude' => 90.4076],
            ['name' => 'Riverside Ground', 'address' => 'Riverside Rd', 'city' => 'Dhaka', 'district' => 'Banani', 'latitude' => 23.7890, 'longitude' => 90.4074],
            ['name' => 'City Stadium', 'address' => 'Stadium Ave', 'city' => 'Chattogram', 'district' => 'Pahartali', 'latitude' => 22.3569, 'longitude' => 91.7832],
            ['name' => 'Eastside Field', 'address' => 'Eastside Rd', 'city' => 'Sylhet', 'district' => 'Kotwali', 'latitude' => 24.8949, 'longitude' => 91.8687],
            ['name' => 'North Arena', 'address' => 'North St', 'city' => 'Dhaka', 'district' => 'Dhanmondi', 'latitude' => 23.7465, 'longitude' => 90.3760],
        ];

        foreach ($locations as $attrs) {
            $loc = Location::create($attrs);

            // create a quick sample match for the first two locations
            if (in_array($loc->name, ['Central Park Pitch', 'Riverside Ground'])) {
                MatchModel::create([
                    'creator_id' => $user->id,
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'title' => $loc->name . ' - Pickup 5v5',
                    'description' => 'Pickup game open to all',
                    'location_id' => $loc->id,
                    'skill_level' => 'mixed',
                    'match_type' => '5v5',
                    'slots_available' => 10,
                    'match_date' => Carbon::now()->addDays(2),
                    'status' => 'open',
                    'visibility' => 'public',
                ]);
            }
        }
    }
}
