<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
        Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder']);

        $this->user = User::first();
        $this->category = Category::first();
        $this->actingAs($this->user);
    }

    public function testItemsDisplayedForAuthenticatedUsers()
    {
        $products = Product::where('user_id', $this->user->id)->get();

        foreach ($products as $product) {
            $product->categories()->attach($this->category->id);
        }

        $likedProduct = $products[0];
        $nonLikedProduct = $products[1];

        $this->user->likes()->attach($likedProduct->id);

        $response = $this->get('/?page=mylist');

        $response->assertSee($likedProduct->name);
    }

    public function testSoldItemsDisplaySoldLabel()
    {
        $soldProduct = Product::where('status', 'sold')->first();
        $availableProduct = Product::where('status', 'available')->first();

        // なければテスト用に一時的に更新
        if (!$soldProduct) {
            $soldProduct = Product::first();
            $soldProduct->update(['status' => 'sold']);
        }

        if (!$availableProduct) {
            $availableProduct = Product::where('id', '!=', $soldProduct->id)->first();
            $availableProduct->update(['status' => 'available']);
        }

        $this->user->likes()->attach($soldProduct->id);

        $response = $this->get('/?page=mylist');
        $response->assertSee('Sold');
        $response->assertSee($soldProduct->name);
    }

    public function testOwnProductsNotDisplayedInMyList()
    {
        $ownProduct = Product::where('user_id', $this->user->id)->first();

        $response = $this->get('/?page=mylist');

        $response->assertDontSee($ownProduct->name);
    }

    public function testNoItemsDisplayedForUnauthenticatedUsers()
    {
        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);

        $response->assertDontSee('<p class="product-card">');
    }
}
