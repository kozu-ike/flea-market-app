<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\UploadedFile;

use Tests\TestCase;

class ProductListingTest extends TestCase
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
     *
     * @return void
     */
    public function testProductListingInfoIsSavedCorrectly()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $this->actingAs($user);
        $category = Category::first();
        $productData = [
            'condition' => '新品・未使用',
            'name' => 'Smartphone',
            'description' => 'Latest model smartphone',
            'price' => 50000,
            'category_ids' => [$category->id],
            'brand' => null,
            'stock' => 1,
        ];

        $image = UploadedFile::fake()->image('smartphone.jpg');

        $response = $this->post(route('sell'), array_merge($productData, ['image' => $image]));
        $response->assertRedirect('/');

        $this->assertDatabaseHas('products', [
            'condition' => '新品・未使用',
            'name' => 'Smartphone',
            'description' => 'Latest model smartphone',
            'price' => 50000,
            'brand' => null,
            'stock' => 1,
        ]);

        $product = Product::where('name', 'Smartphone')->first();
        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => $category->id,
        ]);

        $this->assertNotNull($product->image);
        $this->assertFileExists(public_path('products/' . $product->image));
    }
}
