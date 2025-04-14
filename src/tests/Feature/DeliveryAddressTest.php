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
        $this->seed(); // Seederでデータ投入
    }

    /**
     * 送付先住所変更画面で住所を登録し、その住所が商品購入画面に反映されるか確認
     *
     * @return void
     */
    public function testDeliveryAddressIsReflectedOnPurchasePage()
    {
        // Seederで作成されたユーザーと商品を取得
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first(); // 任意の商品名を選択

        $this->actingAs($user);

        // 送付先住所変更画面を開く
        $response = $this->get(route('purchase.address', ['item_id' => $product->id]));

        // 住所を登録する
        $response = $this->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '1234 Sample St, Sample City, SC 12345',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        // 送付先住所が正しく登録されていることを確認
        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');

        // 商品購入画面を開く
        $response = $this->get(route('purchase', ['item_id' => $product->id]));

        // 登録した住所が商品購入画面に正しく反映されているか確認
        $response->assertSee('1234 Sample St, Sample City, SC 12345');
    }

    /**
     * 商品を購入する際に、送付先住所が購入情報に紐づけられるか確認
     *
     * @return void
     */
    public function testDeliveryAddressIsAssociatedWithPurchase()
    {
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::where('name', 'ショルダーバッグ')->first();

        $this->actingAs($user);

        // 送付先住所変更画面を開く
        $response = $this->get(route('purchase.address', ['item_id' => $product->id]));

        // 住所を登録する
        $response = $this->post(route('purchase.address.update', ['item_id' => $product->id]), [
            'address' => '1234 Sample St, Sample City, SC 12345',
            'postal_code' => '123-4567',
            'building' => 'Sample Building',
        ]);

        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');

        // 商品を購入する
        $response = $this->post(route('purchase', ['item_id' => $product->id]), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHas('address', '1234 Sample St, Sample City, SC 12345');
    }
}
