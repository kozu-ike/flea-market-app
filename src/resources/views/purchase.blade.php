@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="product-show">
        <div class="left-group">
            <div class="product-img">
                <img src="{{ asset('storage/products/'.$product->image) }}" alt="{{ e($product->name) }}">
            </div>

            <div class="product-details">
                <h2>{{ e($product->name) }}</h2>
                <p class="price">¥{{ number_format($product->price) }}（税込）</p>
            </div>

            <div class="product-purchase">
                <label class="contact-form__label" for="payment_method">
                    お支払方法
                </label>
                <form method="POST" action="{{ route('purchase.updatePayment', $product->id) }}" id="payment-form">
                    @csrf
                    <select class="payment-method__select" name="payment_method" required onchange="this.form.submit()">
                        <option disabled selected>選択してください</option>
                        <option value="1" {{ old('payment_method', session('selected_payment')) == 1 ? 'selected' : '' }}>カード支払い</option>
                        <option value="2" {{ old('payment_method', session('selected_payment')) == 2 ? 'selected' : '' }}>コンビニ支払い</option>
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

        <div class="right-group">
            <div class="product-plan">
                <label for="product-plan_price">
                    <p class="price">商品代金</p>
                    <p class="price">¥{{ number_format($product->price) }}</p>
                </label>
                <label for="product-plan_price_payment-method">
                    <p class="payment-method">お支払方法</p>
                    <p class="payment-method">
                        @php
                        $paymentMethod = \App\Models\PaymentMethod::find(session('selected_payment'));
                        @endphp

                        {{-- 支払い方法が見つかればその名前を表示、なければ「未選択」を表示 --}}
                        {{ $paymentMethod ? $paymentMethod->name : '未選択' }}
                    </p>
                </label>
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