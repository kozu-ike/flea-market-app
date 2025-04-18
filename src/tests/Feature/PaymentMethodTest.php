<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
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

    public function testPaymentMethodIsUpdatedImmediatelyOnSelection()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        $this->actingAs($user);

        $response = $this->get(route('purchase', $product->id));
        $response->assertSee('カード支払い');
        $response->assertSee('コンビニ支払い');

        $response = $this->post(route('purchase.updatePayment', $product->id), [
            'payment_method' => 'カード支払い',
        ]);

        $response->assertSessionHas('selected_payment', 'カード支払い');
        $response = $this->get(route('purchase', $product->id));
        $response->assertSee('カード支払い');

        $response = $this->post(route('purchase.updatePayment', $product->id), [
            'payment_method' => 'コンビニ支払い',
        ]);

        $response->assertSessionHas('selected_payment', 'コンビニ支払い');
        $response = $this->get(route('purchase', $product->id));
        $response->assertSee('コンビニ支払い');
    }
}
