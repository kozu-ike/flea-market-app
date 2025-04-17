@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <h1 class="page-title">商品の出品</h1>

    <form action="{{ url('/sell') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="image" class="form-label">商品画像</label>
            <div class="image-upload">
                <input type="file" id="image" name="image" accept="image/*">
                <label for="image" class="image-placeholder">画像を選択する</label>
            </div>
            @error('image')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">カテゴリー</label>
            <div class="category-list">
                @foreach($categories as $category)
                <label class="category-label">
                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}">
                    <span>{{ $category->name }}</span>
                </label>
                @endforeach
            </div>
            @error('category_ids')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="condition" class="form-label">商品の状態</label>
            <select id="condition" name="condition">
                <option value="">選択してください</option>
                @foreach($conditions as $condition)
                <option value="{{ $condition->name }}">{{ $condition->name }}</option>
                @endforeach
            </select>
            @error('condition')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="name" class="form-label">商品名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="brand" class="form-label">ブランド名</label>
            <input type="text" id="brand" name="brand" value="{{ old('brand') }}">
            @error('brand')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="form-label">商品説明</label>
            <textarea id="description" name="description"></textarea>
            @error('description')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="price" class="form-label">販売価格</label>
            <div class="price-input">
                <input type="number" id="price" name="price" value="{{ old('price') }}">
            </div>
            @error('price')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">出品する</button>
    </form>
</div>
@endsection