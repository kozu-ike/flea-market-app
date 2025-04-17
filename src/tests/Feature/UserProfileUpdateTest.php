<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testUserProfileDisplaysInitialValues()
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $user = User::first();

        $this->actingAs($user);

        $response = $this->get(route('profile.setup'));
        $response->assertSee($user->name);
        $response->assertSee($user->profile_image);
        $response->assertSee($user->postal_code);
        $response->assertSee($user->address);
    }
}
