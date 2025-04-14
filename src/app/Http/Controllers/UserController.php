<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Faker\Provider\ar_EG\Address;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

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
        $keyword = $request->keyword; // 検索キーワードを保持

        // タブに応じた商品データを取得
        if ($request->tab == 'sell') {
            $products = $user->products;

            // 検索キーワードがあれば、出品商品をフィルタリング
            if ($keyword) {
                $products = $products->where('name', 'like', '%' . $keyword . '%');
            }

            $purchases = [];  // 購入した商品は空

            // 出品商品がない場合
            if ($products->isEmpty()) {
                $noSellItems = true;  // 出品商品なしフラグ
            }
        } elseif ($request->tab == 'buy') {
            $purchases = $user->orders()->with('product')->get();  // 購入した商品（Orderモデルに変更）

            // 検索キーワードがあれば、購入商品をフィルタリング
            if ($keyword) {
                $purchases = $purchases->filter(function ($purchase) use ($keyword) {
                    return strpos($purchase->product->name, $keyword) !== false;
                });
            }

            $products = [];  // 出品した商品は空

            // 購入商品がない場合
            if ($purchases->isEmpty()) {
                $noBuyItems = true;  // 購入商品なしフラグ
            }
        }

        return view('mypage', compact('user', 'products', 'purchases', 'noSellItems', 'noBuyItems', 'keyword'));
    }



    public function updateProfile(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        // AddressRequest のバリデーション済みデータを取得
        $validatedAddress = $addressRequest->validated();
        $user = auth()->user();

        // ユーザーの基本情報の更新
        $updateData = [
            'name'        => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address'     => $validatedAddress['address'],
            'building'    => $validatedAddress['building'] ?? '',
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
        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました');
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
