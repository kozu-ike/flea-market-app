<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // User と Category をファクトリで作成
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();

        // ユーザーとしてログイン
        $this->actingAs($this->user);
    }

    // 1. 未認証の場合は何も表示されない
    public function testNoItemsDisplayedForUnauthenticatedUsers()
    {
        $response = $this->get('/?page=mylist');
        $response->assertSee('ログイン');
        $response->assertDontSee('<p>'); // アイテムが表示されないことを確認
    }

    // 2. ログインしたユーザーがいいねした商品だけが表示される
    public function testItemsDisplayedForAuthenticatedUsers()
    {
        // 商品を作成し、カテゴリをランダムに関連付ける
        $products = Product::factory()->count(2)->create();
        $likedProduct = $products[0];
        $nonLikedProduct = $products[1];

        // いいねを作成
        $this->user->likes()->create(['product_id' => $likedProduct->id]);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');

        // 商品が正しく表示されるか確認
        $response->assertSee($likedProduct->name);
        $response->assertSee('¥' . number_format($likedProduct->price));

        // いいねしていない商品が表示されないことを確認
        $response->assertDontSee($nonLikedProduct->name);
        $response->assertDontSee('¥' . number_format($nonLikedProduct->price));
    }

    // 3. 購入済み商品には「Sold」と表示される
    public function testSoldItemsDisplaySoldLabel()
    {
        // 購入済み商品を作成（user_id と category_id を必ず指定）
        $soldProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'sold',
            'category_id' => $this->category->id,
        ]);
        $availableProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'available',
            'category_id' => $this->category->id,
        ]);

        // いいねを作成
        $this->user->likes()->create(['product_id' => $soldProduct->id]);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');

        // Soldラベルが表示されることを確認
        $response->assertSee('Sold');

        // 出品中の商品は表示されない
        $response->assertDontSee($availableProduct->name);
    }

    // 4. 自分が出品した商品は表示されない
    public function testOwnProductsNotDisplayedInMyList()
    {
        // ユーザーが出品した商品を作成
        $ownProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');

        // 自分が出品した商品が表示されないことを確認
        $response->assertDontSee($ownProduct->name);
    }

    // 5. 購入済みの商品のみSoldが表示される
    public function testSoldLabelIsDisplayedOnlyForSoldProducts()
    {
        // 購入済み商品を作成（user_id と category_id を必ず指定）
        $soldProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'sold',
            'category_id' => $this->category->id,
        ]);
        $availableProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'available',
            'category_id' => $this->category->id,
        ]);

        // いいねを作成
        $this->user->likes()->create(['product_id' => $soldProduct->id]);

        // マイリストページを開く
        $response = $this->get('/?page=mylist');

        // Sold ラベルが正しく表示されていることを確認
        $content = $response->getContent();
        $soldCount = substr_count($content, 'Sold');

        $this->assertEquals(1, $soldCount, 'Sold ラベルが複数商品に表示されていないか確認');
    }
}
