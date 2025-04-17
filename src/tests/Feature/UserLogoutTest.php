<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    /**
     * @return void
     */
    public function testLogout()
    {
        $user = User::first();

        $response = $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
