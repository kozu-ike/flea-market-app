@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form__content">
    <div class="address-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form action="{{ route('profile.setup') }}" method="POST">
        @csrf
        @method('PUT') <!-- これが必要なのは、更新操作のため -->


        <!-- プロフィール画像 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="profile_image">プロフィール画像</label>
            </div>
            <input type="file" name="profile_image" id="profile_image" class="form__input" accept="image/*" />
            <div class="form__error">
                @error('profile_image')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- ユーザー名 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="name">ユーザー名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required />
                </div>
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <!-- 郵便番号 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="postal_code">郵便番号</label>
            </div>
            <input type="text" name="postal_code" id="postal_code" class="form__input" minlength="7" maxlength="8"
                pattern="\d{3}-?\d{4}" title="例: 123-4567 または 1234567"
                autocomplete="postal-code"
                value="{{ old('postal_code', auth()->user()->postal_code) }}" required />
            <div class="form__error">
                @error('postal_code')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 住所 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="address">住所</label>
            </div>
            <input class="form__input" type="text" name="address" id="address" value="{{ old('address', auth()->user()->address) }}" required />
            <div class="form__error">
                @error('address')
                {{ $message }}
                @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="building">建物名</label>
            </div>
            <input class="form__input" type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}" required />
        </div>

        <!-- 更新ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection