<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>project-beginners-fle-market-app</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <nav class="header-nav">
                <!-- ロゴ -->
                <div class="header-title">
                    <a href="/">
                        <img src="{{ asset('storage/products/logo.svg') }}" alt="coachtech">
                    </a>
                </div>

                <!-- 検索フォーム -->
                <div class="search-form">
                    <form action="/products/search" method="get">
                        <input class="search-form__keyword" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ old('keyword', request('keyword')) }}" onchange="this.form.submit()">
                    </form>
                </div>

                <!-- ナビゲーション（マイページ、ログイン/ログアウト） -->
                <div class="header-nav__item">
                    @if (Auth::check())
                    <form action="/logout" method="post">
                        @csrf
                        <button class="header-nav__button">ログアウト</button>
                    </form>
                    @else
                    <form action="/login" method="get">
                        <button class="header-nav__button">ログイン</button>
                    </form>
                    @endif
                </div>

                <form action="/mypage" method="get">
                    @csrf
                    <button class="header-nav__button">マイページ</button>
                </form>

                <!-- 出品ボタン -->
                <div class="listing">
                    <form action="/sell" method="get">
                        @csrf
                        <button class="listing-button">出品</button>
                </div>
                </form>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>