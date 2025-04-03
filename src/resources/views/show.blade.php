@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="product-show">
        <!-- 商品画像 -->
        <div class="product-img">
            <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
        </div>

        <!-- 商品詳細 -->
        <div class="product-details">
            <h2>{{ e($product->name) }}</h2>
            <p class="brand">{{ e($product->brand) }}</p>
            <p class="price">¥{{ number_format($product->price) }}（税込）</p>

            <!-- いいね機能 -->
            <div class="like-section">
                <form action="{{ route('products.like', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="like-btn {{ $product->likes()->where('user_id', auth()->id())->exists() ? 'liked' : '' }}">
                        <img src="{{ asset('storage/products/like-btn.png') }}" alt="like" width="40" height="40">
                        {{ $product->likes()->count() }}
                    </button>
                </form>
                <div class="comment-section">
                    <img src="{{ asset('storage/products/comment.png') }}" alt="comment" width="40" height="40">
                    {{ $product->comments->count() }}
                </div>
            </div>

            <!-- 購入ボタン -->
            <form action="{{ route('purchase', $product->id) }}" method="GET">
                <button type="submit" class="buy-btn">購入手続きへ</button>
            </form>

            <!-- 商品説明 -->
            <div class="product-description">
                <h3>商品説明</h3>
                <p>{{ e($product->description) }}</p>
            </div>

            <!-- 商品情報 -->
            <div class="product-info">
                <h3>商品情報</h3>
                <p><strong>カテゴリー:</strong>
                    @foreach ($product->categories as $index => $category)
                    {{ e($category->name) }}@if($index < $product->categories->count() - 1), @endif
                        @endforeach
                </p>
                <p><strong>状態:</strong> {{ e($product->condition) }}</p>
            </div>

            <!-- コメント機能 -->
            <div class="comments">
                <h3>コメント ({{ $product->comments->count() }})</h3>
                <ul class="comment-list">
                    @foreach ($product->comments as $comment)
                    <li>
                        <strong>{{ $comment->user->name }}</strong>: {{ e($comment->content) }}
                    </li>
                    @endforeach
                </ul>

                @auth
                <form action="{{ route('comments.store', $product->id) }}" method="POST">
                    @csrf
                    <textarea name="content" required maxlength="255" placeholder="コメントを入力してください"></textarea>
                    <button type="submit">コメントを送信する</button>

                    @if(session('message'))
                    <div class="alert alert-info">
                        {{ session('message') }}
                    </div>
                    @endif
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection