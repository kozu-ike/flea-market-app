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
     * @return void
     */
    public function testPartialMatchSearchByProductName()
    {
        $user = User::factory()->create();

        $product1 = Product::factory()->create(['name' => 'Test Product 1']);
        $product2 = Product::factory()->create(['name' => 'Another Product']);
        $product3 = Product::factory()->create(['name' => 'Test Product 2']);

        $this->actingAs($user);

        $response = $this->get('/products/search?keyword=Test');

        $response->assertSee('Test Product 1');
        $response->assertSee('Test Product 2');
        $response->assertDontSee('Another Product');
    }

    /**
     *
     * @return void
     */
    public function testSearchStateIsPersistedInMyList()
    {
        $searchKeyword = 'ショルダーバッグ';

        session(['search_query' => $searchKeyword]);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('腕時計');
    }
}
