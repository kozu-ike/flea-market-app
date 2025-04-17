<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
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
    }

    /** @test */
    public function all_products_are_displayed_on_product_page()
    {
        $user = \App\Models\User::factory()->create();
        $category = \App\Models\Category::factory()->create();

        $product1 = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Product 1',
            'price' => 1000,
        ]);
        $product1->categories()->attach($category->id);
        $product2 = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Product 2',
            'price' => 2000,
        ]);
        $product2->categories()->attach($category->id);

        $response = $this->get('/');
        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
    }


    /** @test */
    public function purchased_product_is_displayed_as_sold()
    {
        $product = Product::factory()->create(['name' => 'Product 1', 'price' => 1000, 'status' => 'available']);
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
        ]);

        $product->update(['status' => 'sold']);

        $response = $this->get('/');
        $response->assertSee($product->name);
        $response->assertSee('Sold');
    }

    /** @test */
    public function products_user_has_listed_are_not_displayed_for_the_user()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/');
        $response->assertDontSee($product->name);
    }
}
