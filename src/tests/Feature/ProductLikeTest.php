<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductLikeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * @return void
     */
    public function testUserCanLikeProduct()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $initialLikeCount = $product->likes()->count();

        $response = $this->followingRedirects()->post(route('products.like', $product));
        $product->refresh();

        $this->assertEquals($initialLikeCount + 1, $product->likes()->count(), 'Product like count did not increment as expected');

        $response->assertSee('like-btn liked');
        $response->assertSee((string) ($initialLikeCount + 1));
    }

    /**
     * @return void
     */
    public function testUserCanUnLikeProduct()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $initialLikeCount = $product->likes()->count();

        $this->followingRedirects()->post(route('products.like', $product));
        $product->refresh();
        $this->assertEquals($initialLikeCount + 1, $product->likes()->count());

        $response = $this->followingRedirects()->post(route('products.like', $product));
        $product->refresh();
        $this->assertEquals($initialLikeCount, $product->likes()->count());

        $response->assertSee('like-btn');
        $response->assertSee((string) $initialLikeCount);
    }
}
