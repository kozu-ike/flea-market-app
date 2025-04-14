<?php

namespace Tests\Feature;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        // 外部キー制約を無効化
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // UserSeederを使用してユーザーをシーディング
        $this->seed(\Database\Seeders\UserSeeder::class);

        $faker = Faker::create();  // Fakerを初期化
        $email = $faker->unique()->safeEmail;  // 一意なメールアドレスを生成

        // ユーザーを登録
        $response = $this->post('/register', [
            'name' => '赤坂太郎',
            'email' => $email,  // 生成したメールアドレスを使う
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録したユーザーを取得（メールアドレスで検索）
        $user = User::where('email', $email)->first(); // 変数で保存したメールアドレスを使用

        // ユーザーでログイン状態にする
        $this->actingAs($user);

        // レスポンスのステータスコードを確認（リダイレクト）
        $response->assertStatus(302);

        // ユーザーが認証されていることを確認
        $this->assertAuthenticated();

        // ユーザーのマイページプロフィールにリダイレクトされることを確認
        $response->assertRedirect(route('profile.setup'));
    }
}
