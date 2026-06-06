@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email-container">
    <div class="verify-email-heading">
        <p class="verify-email-message">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
    </div>
    <form action="{{ route('verification.send') }}" method="post">
        @csrf
        <div class="verify-email-button">
            <button class="verify-email-button-submit" type="button" onclick="window.open('http://localhost:8025', '_blank')">認証はこちらから</button>
        </div>
        <div class="verify-email-resend-button">
            <button class="verify-email-resend-button-submit" type="submit">認証メールを再送する</button>
        </div>
    </form>
</div>
@endsection