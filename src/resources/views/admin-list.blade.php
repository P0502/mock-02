@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-list.css') }}">
@endsection

@section('content')
<div class="admin-attendance-list">
    <label class="admin-attendance-list-title">{{ $date->format('Y年n月j日') }}の勤怠</label>
    <div class="day-navigation">
        <a href="{{ route('admin.attendance-list', ['date' => $prevDate]) }}" class="day-arrow-navigation-link">←</a>
        <a href="{{ route('admin.attendance-list', ['date' => $prevDate]) }}" class="day-navigation-link">前日</a>
        <span class="date-navigation-current">
            <img src="{{ asset('logo/カレンダー.png') }}" style="width: 20px; height: 23px; vertical-align: middle; margin-right: 5px; margin-bottom: 5px;">
            {{ $date->format('Y/m/d') }}
        </span>
        <a href="{{ route('admin.attendance-list', ['date' => $nextDate]) }}" class="day-navigation-link">翌日</a>
        <a href="{{ route('admin.attendance-list', ['date' => $nextDate]) }}" class="day-arrow-navigation-link">→</a>
    </div>

    <table class="admin-attendance-list-table">
        <tr class="admin-attendance-list-header">
            <th class="admin-attendance-list-header-name-item">名前</th>
            <th class="admin-attendance-list-header-start_time-item">出勤</th>
            <th class="admin-attendance-list-header-end_time-item">退勤</th>
            <th class="admin-attendance-list-header-break_time-item">休憩</th>
            <th class="admin-attendance-list-header-total_hours-item">合計</th>
            <th class="admin-attendance-list-header-detail-item">詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr class="admin-attendance-list-row">
            <td class="admin-attendance-list-user-name">
                <label class="admin-attendance-list-user-name-label">{{ $attendance->user->name }}</label>
            </td>
            <td class="admin-attendance-list-start_time">
                <label class="admin-attendance-list-start_time-label">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</label>
            </td>
            <td class="admin-attendance-list-end_time">
                <label class="admin-attendance-list-end_time-label">{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</label>
            </td>
            <td class="admin-attendance-list-break_time">
                <label class="admin-attendance-list-break_time-label">
                    @php
                    $totalSeconds = 0;
                    foreach($attendance->breaks as $break) {
                    if($break->start_time && $break->end_time) {
                    $start = \Carbon\Carbon::parse($break->start_time)->second(0);
                    $end = \Carbon\Carbon::parse($break->end_time)->second(0);
                    $totalSeconds += $start->diffInSeconds($end);
                    }
                    }

                    $totalMinutes = (int)($totalSeconds / 60);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    @endphp
                    {{ $totalMinutes > 0 ? sprintf('%d:%02d', $hours, $minutes) : '00:00' }}
                </label>
            </td>
            <td class="admin-attendance-list-total_hours">
                <label class="admin-attendance-list-total_hours-label">{{ ($attendance->start_time && $attendance->end_time) ? $attendance->total_working_hours : '' }}</label>
            </td>
            <td class="admin-attendance-detail-link"><a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}" class="admin-attendance-detail-link-inner">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection