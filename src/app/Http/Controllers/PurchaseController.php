<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseAddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function show($itemId)
    {
        $product = Product::findOrFail($itemId);

        $paymentMethods = PaymentMethod::all();

        return view('purchase', compact('product', 'paymentMethods'));
    }

    public function editAddress($itemId)
    {
        $product = Product::findOrFail($itemId);
        return view(
            'address',
            [
                'itemId' => $itemId,
                'product' => $product,
                'user' => Auth::user()
            ]
        );
    }

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
        $paymentMethod = $request->input('payment_method');
        session(['selected_payment' => $paymentMethod]);

        return redirect()->route('purchase', ['item_id' => $productId]);
    }


    public function store(PurchaseRequest $request, $itemId)
    {
        $validated = $request->validated();

        $product = Product::findOrFail($itemId);

        if ($product->stock < 1) {
            return redirect()->back()->with('error', '在庫がありません');
        }


        $paymentMethod = PaymentMethod::find(session('selected_payment'));

        if (!$paymentMethod) {
            return redirect()->back()->with('error', '選択された支払い方法が存在しません');
        }

        $product->decrement('stock');


        Order::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'payment_method_id' => $paymentMethod->id,
        ]);


        Stripe::setApiKey(env('STRIPE_SECRET'));


        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $product->name,
                        ],
                        'unit_amount' => $product->price * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('purchase.success'),
            'cancel_url' => route('purchase.cancel'),
        ]);

        return redirect($session->url);

        if ($product->stock == 0) {
            $product->update([
                'status' => 'sold',
            ]);
        }

        return redirect()->route('mypage')->with('success', '購入が完了しました');
    }
}
