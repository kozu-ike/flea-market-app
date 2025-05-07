<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function password_and_password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function user_can_register_with_valid_input()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->seed(\Database\Seeders\UserSeeder::class);

        $faker = Faker::create();
        $email = $faker->unique()->safeEmail;

        Mail::fake();

        $response = $this->post('/register', [
            'name' => '赤坂太郎',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', $email)->first();
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        $response->assertStatus(302);

        $this->assertAuthenticated();

        $response->assertRedirect(route('profile.setup'));

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'email_verified_at' => now(),
        ]);

        Mail::assertSent(VerifyEmail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }
}
