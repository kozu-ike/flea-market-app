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

        // User シーディングを実行
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    /**
     * ログアウトができる
     *
     * @return void
     */
    public function testLogout()
    {
        // シーダーで作成したユーザーを取得
        $user = User::first();  // シーダーで作成した最初のユーザーを使う

        // ユーザーでログインする
        $response = $this->actingAs($user);

        // ログアウトボタンを押した場合のシミュレーション
        $response = $this->post('/logout');

        // ログアウト後、ホーム画面（またはリダイレクト先）にリダイレクトされることを確認
        $response->assertRedirect('/');

        // ログインしていない状態であることを確認
        $this->assertGuest();
    }
}
