@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form__content">
    <div class="address-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form action="{{ route('updateProfile') }}" method="POST">
        @csrf


        <!-- プロフィール画像 -->
        <div class="form__group">
            <div class="profile">
                <div class="profile-image-container">
                    @if(auth()->user()->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="プロフィール画像">
                    @endif
                </div>

                <label for="profile_image" class="profile-image-upload">画像を選択する</label>
                <input type="file" name="profile_image" id="profile_image" class="form__input" accept="image/*" hidden />

                <div class="form__error">
                    @error('profile_image')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- ユーザー名 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="name">ユーザー名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="name" id="name" class="form__input" value="{{ old('name', auth()->user()->name) }}" required />
                </div>
                <div class="form__error">
                    @error('name')
                    <span class="error-message">{{ $message }}</span>
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
                <span class="error-message">{{ $message }}</span> @enderror
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
                <span class="error-message">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 建物名 -->
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label" for="building">建物名</label>
            </div>
            <input class="form__input" type="text" name="building" id="building" value="{{ old('building', auth()->user()->building) }}" required />
            <div class="form__error">
                @error('building')
                <span class="error-message">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 更新ボタン -->
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection