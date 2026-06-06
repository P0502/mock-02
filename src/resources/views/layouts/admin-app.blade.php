<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin-app.css') }}" />
    @yield('css')
</head>

<body style="background-color: #f1f1f1">
    <header class="header">
        <h1 class="header-logo">
            <img src="{{ asset('logo/COACHTECHヘッダーロゴ (1).png') }}" alt="logo" class="header-logo-image">
        </h1>
        <div class="header-buttons">
            <div class="attendance-list-link">
                <a href="/admin/attendance/list" class="attendance-list-link-inner">勤怠一覧</a>
            </div>
            <div class="attendance-staff-list-link">
                <a href="/admin/staff/list" class="attendance-staff-list-link-inner">スタッフ一覧</a>
            </div>
            <div class="request-attendance-list-link">
                <a href="{{ route('attendance.request') }}" class="request-attendance-list-link-inner">申請一覧</a>
            </div>
            <form class="logout-admin-form" action="/logout" method="post">
                @csrf
                <input type="hidden" name="from" value="admin">
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