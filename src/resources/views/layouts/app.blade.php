<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    @yield('css')
</head>

<body style="background-color: #f1f1f1">
    <header class="header">
        <h1 class="header-logo">
            <img src="{{ asset('logo/COACHTECHヘッダーロゴ (1).png') }}" alt="logo" class="header-logo-image">
        </h1>
        <div class="header-buttons">
            @if($after_work ?? false)
            <div class="attendance-month-list">
                <a href="" class="attendance-month-list-inner">今月の出勤一覧</a>
            </div>
            @else
            <div class="attendance-link">
                <a href="/attendance" class="attendance-link-inner">勤怠</a>
            </div>
            <div class="attendance-list-link">
                <a href="/attendance/list" class="attendance-list-link-inner">勤怠一覧</a>
            </div>
            @endif
            <div class="request-list-link">
                <a href="/stamp_correction_request/list" class="request-list-link-inner">申請</a>
            </div>
            <form class="logout-user-form" action="/logout" method="post">
                @csrf
                <div class="logout-button">
                    <button class="logout-button-submit" type="submit">ログアウト</button>
                </div>
            </form>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>