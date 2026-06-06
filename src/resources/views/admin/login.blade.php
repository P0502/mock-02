@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
@endsection

@section('content')
<div class="login-admin">
    <h2 class="login-admin-heading">管理者ログイン</h2>
    <form class="login-admin-form" action="/admin/login" method="post">
        @csrf
        <div class="login-admin-group">
            <label class="login-admin-email-label">メールアドレス</label>
            <input class="login-admin-email-input" type="text" name="email" id="email" value="{{ old('email') }}">
            <p class="login-admin-error">
                @error('email')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="login-admin-group">
            <label class="login-admin-password-label">パスワード</label>
            <input class="login-admin-password-input" type="password" name="password" id="password" value="{{ old('password') }}">
            <p class="login-admin-error">
                @error('password')
                {{ $message }}
                @enderror
            </p>
        </div>
        <div class="login-admin-button">
            <button class="login-admin-button-submit" type="submit">管理者ログインする</button>
        </div>
    </form>
    @endsection