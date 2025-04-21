<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Log;


class ExhibitionController extends Controller
{
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

        return view('sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $product = new Product();
        $product->name = $validated['name'];
        $product->price = $validated['price'];
        $product->brand = isset($validated['brand']) ? $validated['brand'] : null;
        $product->description = $validated['description'];
        $product->condition = $validated['condition'];
        $product->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->storeAs('products', $filename, 'public');
            $product->image = $filename;
        }

        $product->user_id = auth()->id();
        $product->save();

        if ($request->has('category_ids')) {
            $product->categories()->attach($validated['category_ids']);
        }
        Log::info('Saved Product', ['product' => $product]);

        return redirect('/')->with('success', '商品が出品されました');
    }
}
