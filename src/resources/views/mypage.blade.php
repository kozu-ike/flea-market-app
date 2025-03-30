@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection
@section('content')
<main>
    <div class="profile">
        <div class="profile-image-container">
            <img src="{{ asset('storage/' . (auth()->user()->profile_image ?? 'products/profile.png')) }}">
        </div>
        <div class="profile-name">
            {{ auth()->user()->name }}
        </div>
        <a class="profile-change" href="{{ route('profile.setup') }}">プロフィールを編集</a>
    </div>

    <div class="product-list">
        <nav class="product-nav">
            <a href="/mypage?tab=sell" class="active">出品した商品</a>
            <a href="/mypage?tab=buy">購入した商品</a>
        </nav>

        <div class="product-grid">
            @foreach ($products as $product)
            <div class="product-card">
                <a href="/products/{{ $product->id }}">
                    <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
                    <p>{{ $product->name }}</p>

                    @if ($product->isSoldOut())
                    <span class="sold-label">Sold</span>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
    </div>
</main>
@endsection