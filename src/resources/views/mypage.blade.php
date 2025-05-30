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
            <a href="/mypage?tab=sell&search={{ request('keyword') }}" class="{{ request('tab') == 'sell' ? 'active' : '' }}">出品した商品</a>
            <a href="/mypage?tab=buy&search={{ request('keyword') }}" class="{{ request('tab') == 'buy' ? 'active' : '' }}">購入した商品</a>
        </nav>

        <div class="product-grid">
            @if(request('tab') == 'sell')
            @foreach ($products as $product)
            <div class="product-card">
                <a href="{{ route('products.show', ['id' => $product->id]) }}">
                    <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
                    <p>{{ $product->name }}</p>
                </a>
            </div>
            @endforeach

            @if (isset($noSellItems) && $noSellItems)
            <p>出品中の商品はありません。</p>
            @endif

            @elseif(request('tab') == 'buy')
            @foreach ($purchases as $purchase)
            <div class="product-card">
                <a href="/item/{{ $purchase->product->id }}">
                    <img src="{{ asset('storage/products/'.$purchase->product->image) }}" alt="{{ e($purchase->product->name) }}">
                    <p>{{ $purchase->product->name }}</p>
                </a>
            </div>
            @endforeach
            @elseif (isset($noBuyItems) && $noBuyItems)
            <p>購入した商品はありません。</p>
            @endif
        </div>
    </div>
</main>
@endsection