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

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    }

    /**
     *
     * @return void
     */
    public function testUserCanCompletePurchase()
    {
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

        $response = $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);
        $response->assertRedirect(route('mypage'));
    }

    /**
     *
     * @return void
     */
    public function testPurchasedProductAppearsInProfile()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        $product->status = 'sold';
        $product->save();
        $product->refresh();

        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertSee('Sample Product');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function testPurchasedProductAddedToProfilePurchasedItems()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);
        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        $product->status = 'sold';
        $product->save();

        $order = Order::where('user_id', $user->id)->where('product_id', $product->id)->first();
        $this->assertNotNull($order);

        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertSee($product->name);
        $response->assertSee('購入した商品');

    }
}
