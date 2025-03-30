<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

    public function mypage()
    {
        // プロフィール更新の処理をここに書く（例: updateProfileメソッドを呼ぶ）
        $user = auth()->user();
        $products = $user->products;
        // mypageビューを返す
        return view('mypage', compact('user', 'products'));
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

    // public function showPurchasedItems()
    // {
    //     $purchases = auth()->user()->purchases()->with('product')->get();
    //     return view('purchases', compact('purchases'));
    // }

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
