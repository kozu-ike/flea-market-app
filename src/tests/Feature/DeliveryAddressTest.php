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

        $response = $this->get(route('purchase.address', ['item_id' => $product->id]));
        $response = $this->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '1234 Sample St, Sample City, SC 12345',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');
        $response = $this->get(route('purchase', ['item_id' => $product->id]));
        $response->assertSee('1234 Sample St, Sample City, SC 12345');
    }

    /**
     * @return void
     */
    public function testDeliveryAddressIsAssociatedWithPurchase()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first();

        $this->actingAs($user);

        $response = $this->get(route('purchase.address', ['item_id' => $product->id]));
        $response = $this->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '1234 Sample St, Sample City, SC 12345',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');
        $response = $this->post(route('purchase', ['item_id' => $product->id]), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');
    }
}
