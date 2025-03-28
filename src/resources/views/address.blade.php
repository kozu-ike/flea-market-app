@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form__content">
    <div class="address-form__heading">
        <h2>住所の変更</h2>
    </div>
    <form class="form" action="{{ route('address.update') }}" method="post">
        @csrf


        <!-- 郵便番号 -->
        <div class="form__group">
            <label class="form__label" for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" minlength="7" maxlength="7" pattern="\d{7}" autocomplete="postal-code" value="{{ old('postal_code') }}">
            <div class="form__error">
                @error('postal_code')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 住所 -->
        <div class="form__group">
            <label class="form__label" for="address">住所</label>
            <input class="form__input" type="text" name="address" id="address" value="{{ old('address') }}">
            <div class="form__error">
                @error('address')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <label class="form__label" for="building">建物名</label>
            <input class="form__input" type="text" name="building" id="building" value="{{ old('building') }}">
        </div>

        <!-- 更新ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection