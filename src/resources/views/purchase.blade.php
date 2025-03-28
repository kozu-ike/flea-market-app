@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="product-show">
        <div class="product-main">
            <!-- 商品画像 -->
            <div class="product-img">
                <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
            </div>

            <!-- 商品詳細 -->
            <div class="product-details">
                <h2>{{ e($product->name) }}</h2>
                <p class="price">¥{{ number_format($product->price) }}（税込）</p>

                <div class="product-purchase">
                    <label class="contact-form__label" for="payment_id">
                        お支払方法
                    </label>
                    <div class="payment-method__select-inner">
                        <select class="payment-method__select" name="payment_id" id="payment_id" required>
                            <option disabled selected>選択してください</option>
                            @foreach($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->id }}" {{ old('payment_id') == $paymentMethod->id ? 'selected' : '' }}>
                                {{ $paymentMethod->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="contact-form__error-message">
                        @error('payment_id')
                        {{ $message }}
                        @enderror
                    </p>
                </div>

            </div>
        </div>

        <!-- 配送先 -->
        <div class="product-address">
            <p class="confirm-form__label">配送先</p>
            <p class="confirm-form__data">{{ auth()->user()->address }}</p>
            <input type="hidden" name="address" value="{{ auth()->user()->address }}">
            <div class="form-group_buttons-center">
                <!-- ボタンもリンクではなくボタンスタイルで統一 -->
                <a class="btn btn-link" href="/profile">変更する</a>
            </div>
        </div>
    </div>
    <!-- 小計 -->
    <div class="product-plan">
        <label for="product-plan_price">
            <p class="price">¥{{ number_format($product->price) }}</p>
        </label>
        <label for="product-plan_price_payment-method">
            お支払方法
            @foreach($paymentMethods as $paymentMethod)
            @if(old('payment_id') == $paymentMethod->id)
            <p class="payment-method">{{ $paymentMethod->name }}</p>
            @endif
            @endforeach
        </label>




        <!-- 購入フォーム -->
        <form method="POST" action="{{ route('purchase.process', $product->id) }}">
            @csrf
            <button type="submit" class="btn-submit">購入する</button>
        </form>
    </div>
</div>
@endsection