<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

use Tests\TestCase;

class ProductLikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * @return void
     */
    public function testUserCanLikeProduct()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(); // カテゴリーを作成
        $product = Product::factory()->create([
            'user_id' => $otherUser->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        $response = $this->get(route('products.show', $product));

        $initialLikeCount = $product->likes()->count();

        $response = $this->followingRedirects()->post(route('products.like', $product));

        $product->refresh();

        $this->assertEquals($initialLikeCount + 1, $product->likes()->count(), 'Product like count did not increment as expected');

        $response->assertSee('like-btn liked');  // 「liked」クラスがボタンに追加されていることを確認
        $response->assertSee((string) ($initialLikeCount + 1));  // 現在の「いいね数」を表示
    }

    /**
     * @return void
     */
    public function testUserCanUnLikeProduct()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $otherUser->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        $response = $this->get(route('products.show', $product));

        $initialLikeCount = $product->likes()->count();

        $response = $this->followingRedirects()->post(route('products.like', $product));

        $product->refresh();

        $this->assertEquals($initialLikeCount + 1, $product->likes()->count(), 'Product like count did not increment as expected');

        $response->assertSee('like-btn liked');
        $response = $this->followingRedirects()->post(route('products.like', $product));

        $product->refresh();

        $this->assertEquals($initialLikeCount, $product->likes()->count(), 'Product like count did not decrement as expected');

        $response->assertSee('like-btn');  // 「liked」クラスが削除されていることを確認
        $response->assertSee((string) $initialLikeCount);  // 現在の「いいね数」を表示
    }
}
