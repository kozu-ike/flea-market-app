<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeder を使ってユーザー・商品・カテゴリを準備
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    }

    /**
     * 「購入する」ボタンを押下すると購入が完了する
     *
     * @return void
     */
    public function testUserCanCompletePurchase()
    {
        // テスト用カテゴリを作成
        $category = Category::factory()->create();

        // テスト用ユーザーと商品を作成
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        // ログイン
        $this->actingAs($user);

        // 商品購入画面にアクセス
        $response = $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        // 購入処理が完了したことを確認
        $response->assertRedirect(route('mypage'));

    }

    /**
     * 購入した商品が「プロフィール/購入した商品一覧」に追加されている
     *
     * @return void
     */
    public function testPurchasedProductAppearsInProfile()
    {
        // テスト用カテゴリを作成
        $category = Category::factory()->create();

        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        // 商品を購入
        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        // 購入後、商品の status を 'sold' に更新
        $product->status = 'sold';
        $product->save(); // 商品の status を保存
        // 商品を再取得して最新状態に反映
        $product->refresh();


        // 購入後、プロフィール画面を表示
        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertSee('Sample Product');

        // データベースに注文が保存されていることを確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function testPurchasedProductAddedToProfilePurchasedItems()
    {
        // 1. テスト用ユーザーと商品を作成
        $user = User::factory()->create();
        $category = Category::factory()->create(); // カテゴリーを作成
        $product = Product::factory()->create([
            'user_id' => $user->id,  // 作成したユーザーを関連付け
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        // ログイン
        $this->actingAs($user);

        // 2. 商品購入
        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        // 商品の status を 'sold' に更新
        $product->status = 'sold';
        $product->save();

        $order = Order::where('user_id', $user->id)->where('product_id', $product->id)->first();
        $this->assertNotNull($order);
        // 3. 購入した商品がプロフィールの「購入した商品一覧」に追加されているか確認
        $response = $this->get(route('mypage'));  // マイページへアクセス
        \Log::info($response->getContent());  // レスポンスのHTMLを確認

        // 購入した商品がプロフィールページに表示されているか確認
        $response->assertSee($product->name);  // 購入した商品名が表示されること
        $response->assertSee('購入した商品');  // 購入した商品一覧のタイトルが表示されること

    }
}
