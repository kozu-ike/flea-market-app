@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main>
    <div class="product-list">
        <nav class="product-nav">
            <a href="#" class="active">おすすめ</a>
            @auth
            <a href="?tab=mylist">マイリスト</a>
            @else
            <a href="#" class="inactive">マイリスト</a>
            @endauth
        </nav>

        <div class="product-grid">
            @foreach ($products as $product)
            <div class="product-card">
                <a href="/item/{{ $product->id }}">
                    <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ $product->name }}">
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