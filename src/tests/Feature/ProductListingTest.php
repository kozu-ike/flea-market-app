<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductListingTest extends TestCase
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
     * 商品出品画面で必要な情報を入力し、商品が正しく保存されるか確認
     *
     * @return void
     */
    public function testProductListingInfoIsSavedCorrectly()
    {
        // テスト用ユーザーを取得
        $user = User::where('email', 'akasaka@example.com')->first();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // カテゴリを取得
        $category = Category::first();

        // 出品商品に必要なデータ
        $productData = [
            'condition' => '新品・未使用',            // 商品の状態（新品）
            'name' => 'Smartphone',                   // 商品名
            'description' => 'Latest model smartphone', // 商品の説明
            'price' => 50000,                         // 販売価格
            'category_ids' => [$category->id],        // カテゴリID（配列として送信）
        ];

        // ダミーの画像ファイルをアップロードする
        $image = UploadedFile::fake()->image('smartphone.jpg');

        // 商品出品画面にPOSTリクエストでデータを送信
        $response = $this->post(route('sell'), array_merge($productData, ['image' => $image]));

        // レスポンスが正しくリダイレクトされているか（商品一覧ページ等）
        $response->assertRedirect('/');

        // 商品がデータベースに正しく保存されているか確認
        $this->assertDatabaseHas('products', [
            'user_id' => $user->id,  // 出品したユーザーのID
            'condition' => '新品・未使用', // 商品の状態が保存されている
            'name' => 'Smartphone',   // 商品名が保存されている
            'description' => 'Latest model smartphone', // 商品の説明が保存されている
            'price' => 50000,         // 販売価格が保存されている
        ]);

        // 商品とカテゴリの関連が中間テーブルに正しく保存されているか確認
        $product = Product::where('name', 'Smartphone')->first();
        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,  // 商品ID
            'category_id' => $category->id,  // カテゴリID
        ]);

        // 画像が保存されているか確認
        $this->assertNotNull($product->image);
        $this->assertFileExists(public_path('products/' . $product->image));
    }
}
