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

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->actingAs($this->user);
    }

    public function testItemsDisplayedForAuthenticatedUsers()
    {
        $products = Product::factory()
            ->count(2)
            ->create(['user_id' => $this->user->id]);

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
        $soldProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'sold',
        ]);
        $soldProduct->categories()->attach($this->category->id);

        $availableProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'available',
        ]);
        $availableProduct->categories()->attach($this->category->id);

        $likedProduct = Product::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'sold',
        ]);
        $likedProduct->categories()->attach($this->category->id);

        $this->user->likes()->attach($likedProduct->id);

        $response = $this->get('/?page=mylist');
        $response->assertSee('Sold');
        $response->assertSee($likedProduct->name);
    }

    public function testOwnProductsNotDisplayedInMyList()
    {
        $ownProduct = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $ownProduct->categories()->attach($this->category->id);

        $response = $this->get('/?page=mylist');
        $response->assertDontSee($ownProduct->name);
    }

    public function testNoItemsDisplayedForUnauthenticatedUsers()
    {
        $response = $this->get('/?page=mylist');
        $response->assertSee('ログイン');
        $response->assertDontSee('<p>');
    }
}
