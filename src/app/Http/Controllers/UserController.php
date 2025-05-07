<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth');
        $this->user = auth()->user();
    }

    public function setupProfile()
    {
        return view('profile', ['profile' => $this->user]);
    }

    public function mypage(Request $request)
    {
        $noSellItems = false;
        $noBuyItems = false;
        $products = [];
        $purchases = [];
        $keyword = $request->keyword;

        if ($request->tab == 'sell') {
            $products = $this->user->products;

            if ($keyword) {
                $products = $products->where('name', 'like', '%' . $keyword . '%');
            }

            if ($products->isEmpty()) {
                $noSellItems = true;
            }
        } elseif ($request->tab == 'buy') {
            $purchases = $this->user->orders()->with('product')->get();

            if ($keyword) {
                $purchases = $purchases->filter(function ($purchase) use ($keyword) {
                    return strpos($purchase->product->name, $keyword) !== false;
                });
            }

            if ($purchases->isEmpty()) {
                $noBuyItems = true;
            }
        }

        return view('mypage', compact('products', 'purchases', 'noSellItems', 'noBuyItems', 'keyword'));
    }

    public function updateProfile(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $validatedAddress = $addressRequest->validated();

        $updateData = [
            'name'        => $validatedAddress['name'],
            'postal_code' => $validatedAddress['postal_code'],
            'address'     => $validatedAddress['address'],
            'building'    => $validatedAddress['building'] ?? '',
        ];

        if ($profileRequest->hasFile('profile_image')) {
            if ($this->user->profile_image) {
                Storage::disk('public')->delete($this->user->profile_image);
            }

            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $updateData['profile_image'] = $path;
        }

        $this->user->update($updateData);

        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました');
    }

    public function showPurchasedItems()
    {
        $purchases = $this->user->purchases()->with('product')->get();
        return view('mypage', ['purchases' => $purchases]);
    }

    public function showSoldItems()
    {
        $soldItems = $this->user->products;
        return view('sold', compact('soldItems'));
    }
}

