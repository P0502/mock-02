@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-user">
    <h2 class="login-user-heading">ログイン</h2>
    <form class="login-user-form" action="/login" method="post">
        @csrf
        <div class="login-user-group">
            <label class="login-user-email-label">メールアドレス</label>
            <input class="login-user-email-input" type="text" name="email" id="email" value="{{ old('email') }}">
            <p class="login-user-error">
                @error('email')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="login-user-group">
            <label class="login-user-password-label">パスワード</label>
            <input class="login-user-password-input" type="password" name="password" id="password" value="{{ old('password') }}">
            <p class="login-user-error">
                @error('password')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="login-user-button">
            <button class="login-user-button-submit" type="submit">ログインする</button>
            <a class="register-user-link" href="/register">会員登録はこちら</a>
        </div>
    </form>
@endsection