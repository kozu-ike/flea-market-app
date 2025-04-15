<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Order; // Orderモデルを使用
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder を使ってユーザー・商品・カテゴリを準備
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    }

    /** @test */
    /** @test */
    public function all_products_are_displayed_on_product_page()
    {
        // ユーザーとカテゴリを作成（Factoryで手動生成）
        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::factory()->create();

        // 商品1作成
        $product1 = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Product 1',
            'price' => 1000,
        ]);
        $product1->categories()->attach($category->id);

        // 商品2作成
        $product2 = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Product 2',
            'price' => 2000,
        ]);
        $product2->categories()->attach($category->id);

        // トップページを開く
        $response = $this->get('/');

        // 商品が表示されていることを確認
        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
    }


    /** @test */
    public function purchased_product_is_displayed_as_sold()
    {
        // 商品と購入したユーザーを作成
        $product = Product::factory()->create(['name' => 'Product 1', 'price' => 1000, 'status' => 'available']);
        $user = User::factory()->create();

        // 注文を作成して購入済みにする
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
        ]);

        // 購入済みのステータスを "sold" に更新
        $product->update(['status' => 'sold']);

        // 商品ページにアクセス
        $response = $this->get('/');
        

        // 購入済みの商品が 'Sold' と表示されることを確認
        $response->assertSee($product->name);
        $response->assertSee('Sold'); // isSoldOut() メソッドで 'sold' 状態の商品を表示する
    }

    /** @test */
    public function products_user_has_listed_are_not_displayed_for_the_user()
    {
        // 1. ユーザーと商品を作成
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        // 2. ログインして商品ページを表示
        $response = $this->actingAs($user)->get('/');

        // 3. 自分が出品した商品が一覧に表示されないことを確認
        $response->assertDontSee($product->name);
    }
}
