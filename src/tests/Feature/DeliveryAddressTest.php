<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @return void
     */
    public function testDeliveryAddressIsReflectedOnPurchasePage()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first();

        $this->actingAs($user);
        $this->followingRedirects()->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '福岡県福岡市中央区赤坂1-2-3',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        $response = $this->get(route('purchase', ['item_id' => $product->id]));
        $response->assertSee('福岡県福岡市中央区赤坂1-2-3');
    }

    /**
     * @return void
     */
    public function testDeliveryAddressIsAssociatedWithPurchase()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first();

        $this->actingAs($user);
        $this->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '福岡県福岡市中央区赤坂1-2-3',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        $response = $this->post(route('purchase', ['item_id' => $product->id]), [
            'product_id' => $product->id,
            'quantity' => 1,
            'payment_method' => 'カード払い',
        ]);

        $user->refresh();
        $this->assertEquals('福岡県福岡市中央区赤坂1-2-3', $user->address);
    }
}
