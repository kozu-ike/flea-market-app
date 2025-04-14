<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // User シーディングを実行
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => bcrypt('password123'),        ]);

        $response->assertSessionHasErrors('email'); // メールアドレスが空の場合のエラー
    }

    /** @test */
    public function password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password'); // パスワードが空の場合のエラー
    }

    /** @test */
    public function incorrect_login_information_shows_error_message()
    {
        // User Seeder から作成したユーザーを取得
        $user = User::first();  // シーダーで作成した最初のユーザーを使う

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword', // 間違ったパスワード
        ]);

        $response->assertSessionHasErrors('email'); // メールアドレスかパスワードが間違っている場合
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        // User Seeder から作成したユーザーを取得
        $user = User::first();  // シーダーで作成した最初のユーザーを使う

        $response = $this->post('/login', [
            'email' => $user->email,  // 作成したユーザーのメールアドレス
            'password' => 'password123', // 正しいパスワード
        ]);

        $this->assertAuthenticatedAs($user); // ログインしたユーザーが認証されているか
        $response->assertRedirect('/mypage'); // ログイン後、ユーザーのプロフィールページにリダイレクトされること
    }
}
