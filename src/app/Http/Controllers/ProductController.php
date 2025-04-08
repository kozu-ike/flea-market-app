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

        // `page` パラメータが 'mylist' の場合、マイリストを表示
        if ($request->page == 'mylist' && $user) {
            // ログインしているユーザーの「いいねした商品」を取得
            $products = $user->likes()->with('product')->get()->pluck('product');
        } else {
            // 通常のおすすめ商品一覧を表示
            $products = Product::paginate(10);
        }

        // ビューに渡す
        return view('index', compact('products'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        Log::info("Search request received: {$keyword}");

        $products = $keyword
            ? Product::where('name', 'like', "%{$keyword}%")->get()
            : Product::all();

        return view('index', compact('products'));
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
