<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    // 商品出品フォームの表示
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions'));  // 出品フォームを表示
    }

    // 商品保存処理
    public function store(ExhibitionRequest $request)
    {
        // バリデーションを通過したデータを取得
        $validated = $request->validated();

        // 商品のインスタンスを作成
        $product = new Product();
        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->brand = $validated['brand'];
        $product->description = $validated['description'];
        $product->category_id = $validated['category_id']; // カテゴリーID
        $product->condition = $validated['condition'];

        // 画像がアップロードされている場合
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        // ログインユーザーのIDをセット
        $product->user_id = auth()->id();
        $product->save();

        // 商品が出品されたことをユーザーに通知
        return redirect('/')->with('success', '商品が出品されました');
    }
}
