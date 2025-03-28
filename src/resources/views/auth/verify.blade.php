<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証</title>
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">

</head>

<body>
    <header>
        <div class="header-title">
            <img src="{{ asset('storage/products/logo.svg') }}" alt="logo" width="370" height="36">
        </div>
    </header>

    <div class="email-container">

        <div class="email-body">
            <p><a href="{{ $verificationUrl }}" class="btn">認証はこちらから</a>
        </div>

        <div class="email-revenge">
            <p><a href="{{ $verificationUrl }}">認証メールを再送する</a>
        </div>
    </div>

</body>

</html>