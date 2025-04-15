<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // 商品一覧画面（トップ画面）
    public function index(Request $request)
    {
        $user = auth()->user();
        $keyword = session('search_query');

        if ($request->page == 'mylist' && $user) {
            $likedProducts = $user->likes()->with('product')->get()->pluck('product');
            $listedProducts = $user->products;
            $products = $likedProducts->merge($listedProducts);

            if ($keyword) {
                $products = $products->filter(function ($product) use ($keyword) {
                    return stripos($product->name, $keyword) !== false;
                });
            }
        } else {
            $products = Product::when($user, function ($query) use ($user) {
                return $query->where('user_id', '!=', $user->id);
            })->paginate(10);
        }

        return view('index', compact('products', 'keyword'));
    }


    // ビューに渡す

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // 🔒 キーワードが配列で送られてくる場合の対策
        if (is_array($keyword)) {
            $keyword = implode(' ', $keyword); // 配列ならスペース区切りで文字列に
        }

        session(['search_query' => $keyword]);

        if (app()->isLocal()) {
            Log::info("Search keyword: " . $keyword);
        }
        $products = $keyword
            ? Product::where('name', 'like', "%{$keyword}%")->get()
            : Product::all();

        return view('index', compact('products', 'keyword'));
    }



    // 商品詳細画面

    public function show($id)
    {
        $product = Product::with(['comments.user'])->findOrFail($id);

        $likesCount = $product->likes()->count(); // ← 実際にDBに問い合わせてカウント
        $commentsCount = $product->comments->count();

        return view('show', compact('product', 'likesCount', 'commentsCount'));
    }

    // いいねを付けるメソッド
    public function like(Product $product)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // トグル処理
        if ($user->likes()->where('product_id', $product->id)->exists()) {
            $user->likes()->where('product_id', $product->id)->delete();
        } else {
            $user->likes()->create([
                'product_id' => $product->id,
            ]);
        }
        return redirect()->route('products.show', ['id' => $product->id]);
    }

    public function store(CommentRequest $request, $productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // 商品を取得
        $product = Product::findOrFail($productId);

        // コメントを保存
        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->product_id = $product->id;
        $comment->save();

        return redirect()->route('products.show', ['id' => $product->id]);
    }
}
