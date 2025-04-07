@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="product-show">
        <!-- 左側グループ -->
        <div class="left-group">
            <!-- 商品画像 -->
            <div class="product-img">
                <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
            </div>

            <!-- 商品詳細 -->
            <div class="product-details">
                <h2>{{ e($product->name) }}</h2>
                <p class="price">¥{{ number_format($product->price) }}（税込）</p>
            </div>

            <div class="product-purchase">
                <label class="contact-form__label" for="payment_id">
                    お支払方法
                </label>
                <form method="POST" action="{{ route('purchase.updatePayment', $product->id) }}" id="payment-form">
                    @csrf
                    <select class="payment-method__select" name="payment_method" required onchange="this.form.submit()">
                        <option disabled selected>選択してください</option>
                        <option value="コンビニ支払い" {{ session('payment_method', session('selected_payment')) == 'コンビニ支払い' ? 'selected' : '' }}>コンビニ支払い</option>
                        <option value="カード支払い" {{ session('payment_method', session('selected_payment')) == 'カード支払い' ? 'selected' : '' }}>カード支払い</option>

                    </select>
                </form>
            </div>
            <p class="contact-form__error-message">
                @error('payment_method')
                {{ $message }}
                @enderror
            </p>
            @php
            // セッションから住所情報を取得。もし住所が更新されていれば、それを表示
            $address = session('address', auth()->user()->address);
            $postalCode = session('postal_code', auth()->user()->postal_code);
            $building = session('building', auth()->user()->building);
            @endphp

            <!-- 配送先部分 -->
            <div class="product-address">
                <p class="confirm-form__label">配送先</p>
                <p class="confirm-form__data">{{ $postalCode }}</p>
                <p class="confirm-form__data">{{ $address }}</p>
                <p class="confirm-form__data">
                    @if($building) {{ $building }} @endif
                </p>
                <div class="form-group_buttons-center">
                    <a class="btn btn-link" href="{{ route('purchase.address', ['item_id' => $product->id]) }}">変更する</a>
                </div>
            </div>
        </div>

        <!-- 右側グループ -->
        <div class="right-group">
            <!-- 小計部分 -->
            <div class="product-plan">
                <label for="product-plan_price">
                    <p class="price">商品代金</p>
                    <p class="price">¥{{ number_format($product->price) }}</p>
                </label>
                <label for="product-plan_price_payment-method">
                    <p class="payment-method">お支払方法</p>
                    <p class="payment-method">
                        {{ session('selected_payment') ? session('selected_payment') : '未選択' }}
                    </p>
                </label>
                <!-- 購入フォーム -->
                <form method="POST" action="{{ route('purchase.process', ['item_id' => $product->id]) }}">
                    @csrf

                    <input type="hidden" name="payment_method" value="{{ session('selected_payment') }}">
                    <input type="hidden" name="shipping_address" value="{{ $address }} {{ $postalCode }} {{ $building }}">
                    <button type="submit" class="btn-submit">購入する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection