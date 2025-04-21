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
        $this->middleware('auth');
    }

    public function setupProfile()
    {
        $profile = auth()->user();
        return view('profile', compact('profile'));
    }

    public function mypage(Request $request)
    {
        $user = auth()->user();

        $noSellItems = false;
        $noBuyItems = false;
        $products = [];
        $purchases = [];
        $keyword = $request->keyword;

        if ($request->tab == 'sell') {
            $products = $user->products;

            if ($keyword) {
                $products = $products->where('name', 'like', '%' . $keyword . '%');
            }

            $purchases = [];

            if ($products->isEmpty()) {
                $noSellItems = true;
            }
        } elseif ($request->tab == 'buy') {
            $purchases = $user->orders()->with('product')->get();

            if ($keyword) {
                $purchases = $purchases->filter(function ($purchase) use ($keyword) {
                    return strpos($purchase->product->name, $keyword) !== false;
                });
            }

            $products = [];

            if ($purchases->isEmpty()) {
                $noBuyItems = true;
            }
        }

        return view('mypage', compact('user', 'products', 'purchases', 'noSellItems', 'noBuyItems', 'keyword'));
    }



    public function updateProfile(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $validatedAddress = $addressRequest->validated();
        $user = auth()->user();

        $updateData = [
            'name'        => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address'     => $validatedAddress['address'],
            'building'    => $validatedAddress['building'] ?? '',
        ];

        if ($profileRequest->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $updateData['profile_image'] = $path;
        }

        $user->update($updateData);

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
