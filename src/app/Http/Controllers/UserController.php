<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Faker\Provider\ar_EG\Address;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // public function showProfile()

    // {
    //     $profile = auth()->user();
    //     $products = $profile->products;
    //     return view('mypage', compact('profile', 'products'));
    // }

    public function __construct()
    {
        $this->middleware('auth');  // ログインしているユーザーのみアクセス可能
    }

    public function setupProfile()
    {
        $profile = auth()->user();
        return view('profile', compact('profile'));
    }

    public function mypage(Request $request)
    {
        $user = auth()->user();

        // 初期化
        $noSellItems = false;
        $noBuyItems = false;
        $products = [];
        $purchases = [];

        // タブに応じた商品データを取得
        if ($request->tab == 'sell') {
            $products = $user->products;  // 出品した商品
            $purchases = [];  // 購入した商品は空

            // 出品商品がない場合
            if ($products->isEmpty()) {
                $noSellItems = true;  // 出品商品なしフラグ
            }
        } elseif ($request->tab == 'buy') {
            $purchases = $user->orders()->with('product')->get();  // 購入した商品（Orderモデルに変更）
            $products = [];  // 出品した商品は空

            // 購入商品がない場合
            if ($purchases->isEmpty()) {
                $noBuyItems = true;  // 購入商品なしフラグ
            }
        }

        return view('mypage', compact('user', 'products', 'purchases', 'noSellItems', 'noBuyItems'));
    }




    public function updateProfile(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        // AddressRequest のバリデーション済みデータを取得
        $validatedAddress = $addressRequest->validated();

        // ProfileRequest のバリデーション済みデータを取得
        $validatedProfile = $profileRequest->validated();

        $user = auth()->user();
        $updateData = [
            'name'        => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address'     => $validatedAddress['address'],
            'building'    => $validatedAddress['building'],
        ];

        // プロフィール画像のアップロード処理（存在する場合）
        if ($profileRequest->hasFile('profile_image')) {
            // 古い画像がある場合は削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像を保存
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $updateData['profile_image'] = $path;
        }

        // ユーザー情報を一括更新
        $user->update($updateData);


        // 更新完了後、マイページにリダイレクト
        return redirect()->route('mypage');
    }


    public function showPurchasedItems()
    {
        $user = auth()->user();
        $purchases = $user->purchases()->with('product')->get();

        return view('mypage', compact('user', 'purchases'));
    }

    public function showSoldItems()
    {
        $soldItems = auth()->user()->products;
        return view('sold', compact('soldItems'));
    }
}
