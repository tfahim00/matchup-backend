<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\MatchModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_match(): void
    {
        $user = User::factory()->create();
        $location = Location::create([
            'name' => 'City Ground',
            'address' => '123 Main Street',
            'city' => 'Dhaka',
            'district' => 'Dhanmondi',
            'latitude' => 23.7509,
            'longitude' => 90.3760,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/matches', [
            'title' => 'Evening Friendly',
            'description' => 'Casual game for adults',
            'location_id' => $location->id,
            'skill_level' => 'intermediate',
            'match_type' => '7v7',
            'slots_available' => 10,
            'match_date' => '2026-09-15 18:00:00',
            'visibility' => 'public',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Evening Friendly')
            ->assertJsonPath('data.creator_id', $user->id);

        $this->assertDatabaseHas('matches', [
            'title' => 'Evening Friendly',
            'creator_id' => $user->id,
        ]);
    }

    public function test_user_can_join_and_leave_match(): void
    {
        $creator = User::factory()->create();
        $player = User::factory()->create();
        $location = Location::create([
            'name' => 'Lakeside Arena',
            'address' => 'Park Road',
            'city' => 'Chattogram',
            'district' => 'Khulshi',
            'latitude' => 22.3569,
            'longitude' => 91.7832,
        ]);

        $match = MatchModel::create([
            'creator_id' => $creator->id,
            'title' => 'Weekend Match',
            'description' => 'Friendly showdown',
            'location_id' => $location->id,
            'skill_level' => 'mixed',
            'match_type' => '5v5',
            'slots_available' => 2,
            'match_date' => '2026-09-20 16:30:00',
            'status' => 'open',
            'visibility' => 'public',
        ]);

        $joinResponse = $this->actingAs($player, 'sanctum')->postJson("/api/matches/{$match->id}/join");
        $joinResponse->assertStatus(200)
            ->assertJsonPath('data.user_id', $player->id)
            ->assertJsonPath('data.status', 'joined');

        $this->assertDatabaseHas('match_participants', [
            'match_id' => $match->id,
            'user_id' => $player->id,
            'status' => 'joined',
        ]);

        $match->refresh();
        $this->assertSame(1, $match->slots_available);

        $leaveResponse = $this->actingAs($player, 'sanctum')->postJson("/api/matches/{$match->id}/leave");
        $leaveResponse->assertStatus(200)
            ->assertJsonPath('message', 'Left match successfully');

        $this->assertDatabaseMissing('match_participants', [
            'match_id' => $match->id,
            'user_id' => $player->id,
        ]);

        $match->refresh();
        $this->assertSame(2, $match->slots_available);
    }
}
