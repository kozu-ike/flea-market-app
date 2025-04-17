<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\ProductSeeder::class);
    }

    public function testUserProfileDisplaysCorrectInformation()
    {
        $user = User::first();
        $user->profile_image = 'test-profile.png';
        $user->save();

        $product1 = Product::where('user_id', $user->id)->first();
        $this->assertNotNull($product1, '出品商品が見つかりません');
        $this->assertTrue(
            $product1->categories->contains('name', 'ファッション'),
            '出品商品がファッションカテゴリに属していません'
        );

        $otherUser = User::where('id', '!=', $user->id)->first();
        if (!$otherUser) {
            $otherUser = User::create([
                'name' => '他のユーザー',
                'email' => 'other@example.com',
                'password' => bcrypt('password123'),
            ]);
        }

        $otherProduct = Product::create([
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'テスト用商品説明',
            'brand' => 'TestBrand',
            'image' => 'test.png',
            'condition' => '良好',
            'stock' => 1,
            'user_id' => $otherUser->id,
        ]);
        $this->assertNotNull($otherProduct, '他のユーザーの商品が作成されていません');

        $user->purchases()->attach($otherProduct->id, ['payment_method' => 'カード支払い']);

        $this->actingAs($user);

        $response = $this->get(route('mypage', ['tab' => 'profile']));
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee(
            '<img src="' . asset('storage/' . ($user->profile_image ?? 'products/profile.png')) . '">',
            false
        );
        $response = $this->get(route('mypage', ['tab' => 'sell']));
        $response->assertStatus(200);
        $response->assertSee($product1->name);
        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertStatus(200);
        $response->assertSee($otherProduct->name);
    }
}
