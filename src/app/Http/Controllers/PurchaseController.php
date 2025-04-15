<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseAddressRequest;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    // 商品購入画面
    public function show($itemId)
    {
        $product = Product::findOrFail($itemId);

        // 支払い方法をハードコーディング、または config で管理するなど
        $paymentMethods = ['カード払い', 'コンビニ払い'];

        return view('purchase', compact('product', 'paymentMethods'));
    }

    // 送付先住所変更画面
    public function editAddress($itemId)
    {
        $product = Product::findOrFail($itemId);
        return view('address', ['itemId' => $itemId, 'product' => $product, 'user' => Auth::user()]);
    }

    // 送付先住所変更処理

    public function updateAddress(PurchaseAddressRequest $request, $itemId)
    {
        $validated = $request->validated();

        $user = auth()->user();

        $updateData = [
            'postal_code' => $validated['postal_code'],
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? '',
        ];

        session([
            'address' => $validated['address'],
            'postal_code' => $validated['postal_code'],
            'building' => $validated['building'],
        ]);

        $user->update($updateData);
        return redirect()->route('purchase', ['item_id' => $itemId]);
    }

    public function updatePayment(Request $request, $productId)
    {
        // 支払い方法をセッションに保存
        $paymentMethod = $request->input('payment_method');
        session(['selected_payment' => $paymentMethod]);

        // ここで何らかの処理を追加することができます (例: 購入処理)
        return redirect()->route('purchase', ['item_id' => $productId]);
    }


    public function store(PurchaseRequest $request, $itemId)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        $product = Product::findOrFail($itemId);

        // 在庫チェック
        if ($product->stock < 1) {
            return redirect()->back()->with('error', '在庫がありません');
        }

        // 在庫を減らす
        $product->decrement('stock');

        // 購入情報を保存
        Order::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'payment_method' => $validated['payment_method'],
        ]);

        if ($product->stock == 0) {
            $product->update([
                'status' => 'sold',
            ]);
        }

        return redirect()->route('mypage')->with('success', '購入が完了しました');
    }
}
