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

        // 必要なSeederを実行
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
        Artisan::call('db:seed', ['--class' => 'CategorySeeder']);
        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
    }

    /**
     * 支払い方法選択画面で変更が即時反映される
     *
     * @return void
     */
    public function testPaymentMethodIsUpdatedImmediatelyOnSelection()
    {
        // Seederからユーザーと商品を取得
        $user = User::where('email', 'akasaka@example.com')->first();
        $product = Product::first();

        // ユーザーをログインさせる
        $this->actingAs($user);

        // 商品購入ページを開く
        $response = $this->get(route('purchase', $product->id));

        // 支払い方法の選択肢が「カード払い」と「コンビニ払い」の2つであることを確認
        $response->assertSee('カード支払い');
        $response->assertSee('コンビニ支払い');

        // プルダウンメニューから「カード払い」を選択
        $response = $this->post(route('purchase.updatePayment', $product->id), [
            'payment_method' => 'カード支払い',
        ]);

        // セッションに選択した支払い方法が保存されていることを確認
        $response->assertSessionHas('selected_payment', 'カード支払い');
        $this->assertEquals('カード支払い', session('selected_payment'));

        // 「コンビニ払い」を選択しても動作確認
        $response = $this->post(route('purchase.updatePayment', $product->id), [
            'payment_method' => 'コンビニ支払い',
        ]);

        // セッションに「コンビニ払い」が保存されていることを確認
        $response->assertSessionHas('selected_payment', 'コンビニ支払い');
        $this->assertEquals('コンビニ支払い', session('selected_payment'));
    }
}
