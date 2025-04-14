<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!User::where('email', 'akasaka@example.com')->exists()) {
            User::create([
                'name' => '赤坂太郎',
                'email' => 'akasaka@example.com',
                'password' => bcrypt('password123'),
                'address' => '福岡県福岡市中央区赤坂1-2-3',
                'postal_code' => '123-4567',
                'building' => '赤坂ビル',
            ]);
        }

        // Seeder を使ってユーザー・商品・カテゴリを準備
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * 必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
     *
     * @return void
     */
    public function testProductDetailPageDisplaysNecessaryInformation()
    {
        // テスト用ユーザーを取得
        $user = User::updateOrCreate(
            ['email' => 'akasaka@example.com'],
            [
                'name' => '赤坂太郎',
                'password' => bcrypt('password123'),
                'address' => '福岡県福岡市中央区赤坂1-2-3',
                'postal_code' => '123-4567',
                'building' => '赤坂ビル',
            ]
        );
        $product = Product::first();

        // カテゴリを取得
        $category1 = Category::create(['name' => 'Category 1']);
        $category2 = Category::create(['name' => 'Category 2']);
        $product->categories()->sync([$category1->id, $category2->id]);


        // コメントを作成
        $comment = Comment::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'content' => 'This is a comment on the product.',
        ]);

        // いいねを作成
        $product->likes()->attach($user->id, ['created_at' => now(), 'updated_at' => now()]);


        // ログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('products.show', $product));

        // 商品名、ブランド名、価格、商品説明が表示されていることを確認
        $response->assertSee($product->name);
        $response->assertSee($product->brand);
        $response->assertSee('¥' . number_format($product->price) . '（税込）');
        $response->assertSee($product->description);

        $response->assertSee($product->condition);
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
        $response->assertSee("コメント ({$product->comments->count()})");

        $response->assertSee($comment->content);
        $response->assertSee($comment->user->name);
        $response->assertSee($product->likes->count());
        $response->assertSee($product->image);
    }

    /**
     * 複数選択されたカテゴリが表示されているか
     *
     * @return void
     */
    public function testMultipleCategoriesAreDisplayedOnProductDetailPage()
    {
        // テスト用ユーザーを取得
        $user = User::firstOrCreate(
            ['email' => 'akasaka@example.com'],
            [
                'name' => '赤坂太郎',
                'password' => bcrypt('password123'),
                'address' => '福岡県福岡市中央区赤坂1-2-3',
                'postal_code' => '123-4567',
                'building' => '赤坂ビル',
            ]
        );
        // 商品を作成
        $product = Product::first();  // ProductSeeder でシードされた最初の商品を取得


        // カテゴリを作成
        $category1 = Category::create(['name' => 'Category 1']);
        $category2 = Category::create(['name' => 'Category 2']);

        // 商品にカテゴリを関連付ける
        $product->categories()->attach([$category1->id, $category2->id]);

        // ログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('products.show', $product));

        // 複数選択されたカテゴリが表示されていることを確認
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }
}
