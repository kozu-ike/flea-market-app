<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\PaymentMethod;
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
        Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder']);
    }

    public function testUserCanCompletePurchase()
    {
        $category = Category::where('name', 'ファッション')->first();
        $user = User::first();
        $paymentMethod = PaymentMethod::where('name', 'カード支払い')->first();

        $product = Product::create([
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
            'user_id' => $user->id,
            'condition' => '新品',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        $response = $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => $paymentMethod->name,
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        $response->assertRedirect('/');
    }

    public function testPurchasedProductAppearsInProfile()
    {
        $category = Category::where('name', 'ファッション')->first();
        $user = User::first();
        $paymentMethod = PaymentMethod::where('name', 'カード支払い')->first();

        $product = Product::create([
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
            'user_id' => $user->id,
            'condition' => '新品',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);

        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => $paymentMethod->name,
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        $product->status = 'sold';
        $product->save();

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertSee($product->name);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method_id' => $paymentMethod->id,
        ]);
    }

    public function testPurchasedProductAddedToProfilePurchasedItems()
    {
        $user = User::first();
        $category = Category::where('name', 'ファッション')->first();
        $paymentMethod = PaymentMethod::where('name', 'カード支払い')->first();

        $product = Product::create([
            'name' => 'Sample Product',
            'price' => 10000,
            'brand' => 'Sample Brand',
            'description' => 'This is a sample product description.',
            'user_id' => $user->id,
            'condition' => '新品',
        ]);
        $product->categories()->attach($category->id);

        $this->actingAs($user);
        $this->post(route('purchase.process', ['item_id' => $product->id]), [
            'payment_method' => $paymentMethod->name,
            'address' => '東京都渋谷区1-1-1',
            'postal_code' => '150-0001',
        ]);

        $product->status = 'sold';
        $product->save();

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method_id' => $paymentMethod->id,
        ]);

        $this->assertNotNull($order);

        $response = $this->get(route('mypage', ['tab' => 'buy']));
        $response->assertSee($product->name);
        $response->assertSee('購入した商品');
    }
}
