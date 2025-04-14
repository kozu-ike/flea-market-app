<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    // 商品出品フォームの表示
    public function create()
    {
        $categories = Category::all();
        $conditions = collect([
            (object)['name' => '新品・未使用'],
            (object)['name' => '未使用に近い'],
            (object)['name' => '目立った傷や汚れなし'],
            (object)['name' => 'やや傷や汚れあり'],
            (object)['name' => '傷や汚れあり'],
            (object)['name' => '全体的に状態が悪い'],
        ]);

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
        $product->condition = $validated['condition'];



        // 画像がアップロードされている場合
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName(); // 元のファイル名を取得

            // 画像を public ディスクに保存（products フォルダではなく直接保存）
            $file->move(public_path('products'), $filename);
            $product->image = $filename;
        }

        // ログインユーザーのIDをセット
        $product->user_id = auth()->id();
        $product->save();

        if ($request->has('category_ids')) {
            $product->categories()->attach($validated['category_ids']);
        }

        // 商品が出品されたことをユーザーに通知
        return redirect('/mypage')->with('success', '商品が出品されました');
    }
}
