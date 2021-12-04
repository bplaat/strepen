<?php

namespace Tests\Feature;

use App\Http\LiveWire\Home;
use App\Models\User;
use Tests\TestCase;

class LeaderboardsTest extends TestCase
{
    // Test leaderboards failing with only seeded data
    public function test_leaderboards()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('leaderboards'))->assertStatus(500); // TODO
    }
}
