<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;  // ここを追加

use Tests\TestCase;

class ProductLikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder を使ってユーザー・商品・カテゴリを準備
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * ユーザーが商品をいいねできるかをテスト
     *
     * @return void
     */
    public function testUserCanLikeProduct()
    {
        // 1. テスト用ユーザーを作成
        $user = User::factory()->create();

        // 2. 商品を作成
        $category = Category::factory()->create(); // カテゴリーを作成
        $product = Product::factory()->create([
            'user_id' => $user->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        // 3. ログイン
        $this->actingAs($user);

        // 4. 商品詳細ページを開く
        $response = $this->get(route('products.show', $product));

        // 初期状態のいいね数を取得
        $initialLikeCount = $product->likes()->count();

        // 5. いいねアイコンを押下（いいねする）
        $response = $this->post(route('products.like', $product));

        // 商品をリフレッシュして再取得
        $product->refresh();

        // 6. いいね数が1増加していることを確認
        $this->assertEquals($initialLikeCount + 1, $product->likes()->count(), 'Product like count did not increment as expected');

        // 7. アイコンの色が変わっていることを確認
        $response->assertSee('like-btn liked');  // 「liked」クラスがボタンに追加されていることを確認

        // 8. いいね数が表示されていることを確認
        $response->assertSee((string) ($initialLikeCount + 1));  // 現在の「いいね数」を表示
    }

    /**
     * ユーザーが商品をいいね解除できるかをテスト
     *
     * @return void
     */
    public function testUserCanUnLikeProduct()
    {
        // 1. テスト用ユーザーを作成
        $user = User::factory()->create();

        // 2. 商品を作成
        $category = Category::factory()->create(); // カテゴリーを作成
        $product = Product::factory()->create([
            'user_id' => $user->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        // 3. ログイン
        $this->actingAs($user);

        // 4. 商品詳細ページを開く
        $response = $this->get(route('products.show', $product));

        // 初期状態のいいね数を取得
        $initialLikeCount = $product->likes()->count();

        // 5. いいねアイコンを押下（いいねする）
        $response = $this->post(route('products.like', $product));

        // 商品をリフレッシュして再取得
        $product->refresh();

        // 6. いいね数が1増加したことを確認
        $this->assertEquals($initialLikeCount + 1, $product->likes()->count(), 'Product like count did not increment as expected');

        // 7. アイコンの色が変わっていることを確認
        $response->assertSee('like-btn liked');  // 「liked」クラスがボタンに追加されていることを確認

        // 8. 再度いいねアイコンを押下（いいね解除）
        $response = $this->post(route('products.like', $product));

        // 商品をリフレッシュして再取得
        $product->refresh();

        // 9. いいね数が1減少したことを確認
        $this->assertEquals($initialLikeCount, $product->likes()->count(), 'Product like count did not decrement as expected');

        // 10. アイコンの色が元に戻っていることを確認
        $response->assertSee('like-btn');  // 「liked」クラスが削除されていることを確認

        // 11. いいね数が減少して表示されていることを確認
        $response->assertSee((string) $initialLikeCount);  // 現在の「いいね数」を表示
    }
}
