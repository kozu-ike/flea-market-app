@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form__content">
    <div class="address-form__heading">
        <h2>住所の変更</h2>
    </div>
    <form action="{{ route('purchase.address.update', ['item_id' => $itemId]) }}" method="POST">
        @csrf
        <!-- 郵便番号 -->
        <div class="form__group">
            <label class="form__label" for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form__input" minlength="7" maxlength="8"
                pattern="\d{3}-?\d{4}" title="例: 123-4567 または 1234567"
                autocomplete="postal-code" value="{{ old('postal_code', auth()->user()->postal_code) }}">
            <div class="form__error">
                @error('postal_code')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 住所 -->
        <div class="form__group">
            <label class="form__label" for="address">住所</label>
            <input class="form__input" type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}">
            <div class="form__error">
                @error('address')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <label class="form__label" for="building">建物名</label>
            <input class="form__input" type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}">
        </div>
        <div class="form__error">
            @error('building')
            {{ $message }}
            @enderror
        </div>

        <!-- 更新ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection