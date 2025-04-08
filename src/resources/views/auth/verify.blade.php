@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('メール認証') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ __('認証メールを再送信しました。') }}
                    </div>
                    @endif

                    {{ __('登録していただいたメールアドレスに認証メールを送付しました。') }}
                    {{ __('メール認証を完了してください。') }}

                    <br><br>
                    <a href="{{ route('verification.resend') }}" class="btn btn-link">
                        {{ __('認証メールを再送する') }}
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection