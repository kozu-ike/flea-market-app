<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\PaymentMethod;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
        Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder']);
    }

    /** @test */
    public function all_products_are_displayed_on_product_page()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $category = Category::first();

        $product1 = Product::where('name', 'ショルダーバッグ')->first();
        $product2 = Product::where('name', '腕時計')->first();

        $product1->categories()->sync([$category->id]);
        $product2->categories()->sync([$category->id]);

        $response = $this->get('/');
        $response->assertSee($product1->name);
        $response->assertSee($product2->name);
    }

    /** @test */
    public function purchased_product_is_displayed_as_sold()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $paymentMethod = PaymentMethod::where('name', 'カード支払い')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first();
        $category = Category::where('name', 'ファッション')->first();
        $product->categories()->attach($category->id);
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method_id' => $paymentMethod->id,
        ]);
        $product->update(['status' => 'sold']);
        $response = $this->get('/');
        $response->assertSee($product->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function products_user_has_listed_are_not_displayed_for_the_user()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('user_id', $user->id)->first();

        $response = $this->actingAs($user)->get('/');
        $response->assertDontSee($product->name);
    }
}
