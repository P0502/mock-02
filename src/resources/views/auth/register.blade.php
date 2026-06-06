@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-user">
    <h2 class="register-user-heading">会員登録</h2>
    <form class="register-user-form" action="/register" method="post">
        @csrf
        <div class="register-user-group">
            <label class="register-user-name-label">名前</label>
            <input class="register-user-name-input" type="text" name="name" id="name" value="{{ old('name') }}">
            <p class="register-user-error">
                @error('name')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="register-user-group">
            <label class="register-user-email-label">メールアドレス</label>
            <input class="register-user-email-input" type="text" name="email" id="email" value="{{ old('email') }}">
            <p class="register-user-error">
                @error('email')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="register-user-group">
            <label class="register-user-password-label">パスワード</label>
            <input class="register-user-password-input" type="password" name="password" id="password" value="{{ old('password') }}">
            <p class="register-user-error">
                @error('password')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="register-user-group">
            <label class="register-user-password_confirmation-label">パスワード確認</label>
            <input class="register-user-password_confirmation-input" type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}">
            <p class="register-user-error">
                @error('password_confirmation')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="register-user-button">
            <button class="register-user-button-submit" type="submit">登録する</button>
            <a class="login-user-link" href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection