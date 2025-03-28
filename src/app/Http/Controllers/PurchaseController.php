<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    // 商品購入画面
    public function show($itemId)
    {
        $product = Product::findOrFail($itemId);
        $paymentMethods = Payment::all();
        return view('purchase.show', compact('product', 'paymentMethods'));
    }

    // 送付先住所変更画面
    public function editAddress($itemId)
    {
        $product = Product::findOrFail($itemId);
        return view('address', compact('product'));
    }

    // 送付先住所変更処理
    public function updateAddress(AddressRequest $request, $itemId)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $user->update([
            'address' => $validated['address'],
        ]);

        return redirect()->route('purchase', ['item_id' => $itemId]);
    }

    // 商品購入処理
    public function store(PurchaseRequest $request, $itemId)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'ログインが必要です');
        }

        $validated = $request->validated();

        $product = Product::findOrFail($itemId);

        if ($product->stock < 1) {
            return redirect('/purchase/' . $itemId)->with('error', '在庫がありません');
        }

        $product->decrement('stock');

        return redirect('/')->with('success', '購入が完了しました');
    }
}
