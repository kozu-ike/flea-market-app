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
    public function index(Request $request)
    {
        $user = auth()->user();
        $keyword = session('search_query');

        if ($request->page == 'mylist' && $user) {
            $likedProducts = $user->likes;
            $products = $likedProducts;

            if ($keyword) {
                $products = $products->filter(function ($product) use ($keyword) {
                    return stripos($product->name, $keyword) !== false;
                });
            }

            return view('index', compact('products', 'keyword'));
        } else {
            $products = Product::when($user, function ($query) use ($user) {
                return $query->where('user_id', '!=', $user->id);
            })->paginate(10);
        }

        return view('index', compact('products', 'keyword'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (is_array($keyword)) {
            $keyword = implode(' ', $keyword);
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

    public function show($id)
    {
        $product = Product::with(['comments.user'])->findOrFail($id);

        $likesCount = $product->likes()->count();
        $commentsCount = $product->comments->count();

        return view('show', compact('product', 'likesCount', 'commentsCount'));
    }

    public function like(Product $product)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->likes()->where('product_id', $product->id)->exists()) {
            $user->likes()->detach($product->id);
        } else {
            $user->likes()->attach($product->id);
        }

        return redirect()->route('products.show', ['id' => $product->id]);
    }

    public function store(CommentRequest $request, $productId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($productId);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->product_id = $product->id;
        $comment->save();

        return redirect()->route('products.show', ['id' => $product->id]);
    }
}
