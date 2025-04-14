<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィールページにアクセスした際、初期値が正しく表示されるか確認
     *
     * @return void
     */
    public function testUserProfileDisplaysInitialValues()
    {
        // シーダーを使ってユーザーを作成
        $this->seed(\Database\Seeders\UserSeeder::class);

        // シーダーで作成したユーザーを取得
        $user = User::first(); // シーダーで作成された最初のユーザー

        // ログイン
        $this->actingAs($user);

        // 🔧 ルート名修正
        $response = $this->get(route('profile.setup'));

        // ユーザー情報が表示されているか確認
        $response->assertSee($user->name);
        $response->assertSee($user->profile_image);
        $response->assertSee($user->postal_code);
        $response->assertSee($user->address);
    }
}
