<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 「商品名」で部分一致検索ができる
     *
     * @return void
     */
    public function testPartialMatchSearchByProductName()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create();  // 既存のUserSeederを使う

        // 商品をいくつか作成（Seederで作成された商品を使う）
        $product1 = Product::factory()->create(['name' => 'Test Product 1']);
        $product2 = Product::factory()->create(['name' => 'Another Product']);
        $product3 = Product::factory()->create(['name' => 'Test Product 2']);

        // ログイン
        $this->actingAs($user);

        // 検索欄に「Test」と入力し検索ボタンを押す
        $response = $this->get('/products/search?keyword=Test');

        // 検索結果として部分一致する商品が表示されることを確認
        $response->assertSee('Test Product 1');
        $response->assertSee('Test Product 2');
        $response->assertDontSee('Another Product');
    }

    /**
     * 検索状態がマイリストでも保持されている
     *
     * @return void
     */
    public function testSearchStateIsPersistedInMyList()
    {
        // 商品名に合わせたキーワード（Seederで作成した商品名）
        $searchKeyword = 'ショルダーバッグ'; // ショルダーバッグの名前を検索

        // セッションに保存
        session(['search_query' => $searchKeyword]);

        // マイリストページに遷移
        $response = $this->get('/?page=mylist');

        // 検索結果が表示されることを確認
        $response->assertStatus(200);
        $response->assertSee('ショルダーバッグ'); // 検索結果に「ショルダーバッグ」が含まれていることを確認
        $response->assertDontSee('腕時計'); // 部分一致しない商品は表示されないことを確認
    }
}
