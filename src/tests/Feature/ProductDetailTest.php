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

        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * @return void
     */
    public function testProductDetailPageDisplaysNecessaryInformation()
    {
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

        $category1 = Category::create(['name' => 'Category 1']);
        $category2 = Category::create(['name' => 'Category 2']);
        $product->categories()->sync([$category1->id, $category2->id]);

        $comment = Comment::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'content' => 'This is a comment on the product.',
        ]);

        $product->likes()->attach($user->id, ['created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user);

        $response = $this->get(route('products.show', $product));
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
     * @return void
     */
    public function testMultipleCategoriesAreDisplayedOnProductDetailPage()
    {
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
        $product = Product::first();

        $category1 = Category::create(['name' => 'Category 1']);
        $category2 = Category::create(['name' => 'Category 2']);

        $product->categories()->attach([$category1->id, $category2->id]);

        $this->actingAs($user);

        $response = $this->get(route('products.show', $product));
        $response->assertSee($category1->name);
        $response->assertSee($category2->name);
    }
}
