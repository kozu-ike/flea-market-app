<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
    }

    public function testPartialMatchSearchByProductName()
    {
        $user = User::first();
        $category = Category::first();

        $product1 = Product::create([
            'name' => 'Test Product 1',
            'price' => 5000,
            'description' => 'This is a test product.',
            'user_id' => $user->id,
            'condition' => '新品',
        ]);
        $product1->categories()->attach($category->id);

        $product2 = Product::create([
            'name' => 'Another Product',
            'price' => 7000,
            'description' => 'Another product description.',
            'user_id' => $user->id,
            'condition' => '中古',
        ]);
        $product2->categories()->attach($category->id);

        $product3 = Product::create([
            'name' => 'Test Product 2',
            'price' => 6000,
            'description' => 'This is another test product.',
            'user_id' => $user->id,
            'condition' => '新品',
        ]);
        $product3->categories()->attach($category->id);

        $this->actingAs($user);

        $response = $this->get('/products/search?keyword=Test');

        $response->assertSee('Test Product 1');
        $response->assertSee('Test Product 2');
        $response->assertDontSee('Another Product');
    }

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
