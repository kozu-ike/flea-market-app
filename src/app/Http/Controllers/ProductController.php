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
    public function index()
    {
        $products = Product::paginate(10);
        return view('index', compact('products'));
    }


//     public function index(Request $request)
// {
//     // クエリパラメータで 'mylist' が指定されている場合は、マイリストビューを返す
//     if ($request->has('page') && $request->page === 'mylist') {
//         // お気に入り商品など、必要なデータを取得（ここでは例として全商品）
//         $products = Product::all(); // すべての商品を取得する場合
//         return view('mylist', compact('products'));
//     }

//         // デフォルトでトップ画面を表示（ページネーションなし）
//         return view('index', compact('products'));}


    // public function mypage(Request $request)
    // {
    //     $view = 'mypage';
    //     if ($request->has('page')) {
    //         $view = $request->page;
    //     }

    //     return view($view);
    // }

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
        $product = Product::findOrFail($id);
        $likesCount = $product->likes_count; // 動的プロパティ

        // 商品に対するコメントの数
        $commentsCount = $product->comments->count();
        return view('show', compact('product'));
    }

    // いいねを付けるメソッド
    public function like($productId)
    {

        if (!auth()->check()) {
            return redirect()->route('login')->with('message', 'ログインしてください');
        }

        // 商品を取得
        $product = Product::findOrFail($productId);
        // ユーザーがその商品に対して「いいね」をしているか確認
        $user = auth()->user();

        // いいねの状態をトグル（もしいいねが付いていれば削除、付いていなければ追加）
        if ($user->likes()->where('product_id', $product->id)->exists()) {
            // 既に「いいね」している場合は解除
            $user->likes()->where('product_id', $product->id)->delete();
        } else {
            // まだ「いいね」していなければ追加
            $user->likes()->create(['product_id' => $product->id]);
        }

        // リダイレクトなど必要に応じて処理を行う
        return back(); // 商品ページにリダイレクト
    }

    public function store(CommentRequest $request, $productId)
    {
        // ユーザーがログインしていない場合はログインページにリダイレクト
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'ログインしてください');
        }

        // ログインしている場合はコメントを保存
        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id(); // ログインユーザーのID
        $comment->product_id = $productId;
        $comment->save();

        return redirect()->back()->with('message', 'コメントが投稿されました');
    }
}
