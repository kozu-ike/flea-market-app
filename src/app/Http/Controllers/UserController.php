<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
    public function showProfile()

    {
        $profile = auth()->user();
        $products = $profile->products;
        return view('mypage', compact('profile', 'products'));
    }

    public function __construct()
    {
        $this->middleware('auth');  // ログインしているユーザーのみアクセス可能
    }

    public function setupProfile()
    {
        $profile = auth()->user();
        return view('profile', compact('profile'));
    }

    public function updateProfile(ProfileRequest $request)
    {

        $validated = $request->validated();

        // ユーザー情報を更新
        $user = auth()->user();
        $user->update([
            'name' => $validated['name'],
            'postal_code' => $validated['postal_code'],
            'address' => $validated['address'],
            'building' => $validated['building'],
        ]);

        // 更新完了後、マイページにリダイレクト
        return redirect()->route('profile');
    }

    public function showPurchasedItems()
    {
        $purchases = auth()->user()->purchases()->with('product')->get();
        return view('purchases', compact('purchases'));
    }

    public function showSoldItems()
    {
        $soldItems = auth()->user()->products;
        return view('sold', compact('soldItems'));
    }
}
