@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff.css') }}">
@endsection

@section('content')
<div class="staff-attendance-list">
    <h2 class="staff-attendance-list-title">{{ $user->name }}さんの勤怠</h2>
    <div class="month-navigation">
        <a href="{{ route('admin.staff', ['id' => $user->id, 'month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="month-arrow-navigation-link">←</a>
        <a href="{{ route('admin.staff', ['id' => $user->id, 'month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="month-navigation-link">前月</a>
        <span class="month-navigation-current">
            <img src="{{ asset('logo/カレンダー.png') }}" style="width: 20px; height: 23px; vertical-align: middle; margin-right: 5px; margin-bottom: 5px;">
            {{ $date->format('Y/m') }}
        </span>
        <a href="{{ route('admin.staff', ['id' => $user->id, 'month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="month-navigation-link">翌月</a>
        <a href="{{ route('admin.staff', ['id' => $user->id, 'month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="month-arrow-navigation-link">→</a>
    </div>
    <table class="staff-attendance-list-table">
        <tr class="staff-attendance-list-header-row">
            <th class="staff-attendance-list-header-date-item">
                <div class="staff-attendance-list-header-date">
                    <label class="staff-attendance-list-header-date-label">日付</label>
                </div>
            </th>
            <th class="staff-attendance-list-header-start_time-item">
                <div class="staff-attendance-list-header-start_time">
                    <label class="staff-attendance-list-header-start_time-label">出勤</label>
                </div>
            </th>
            <th class="staff-attendance-list-header-end_time-item">
                <div class="staff-attendance-list-header-end_time">
                    <label class="staff-attendance-list-header-end_time-label">退勤</label>
                </div>
            </th>
            <th class="staff-attendance-list-header-break_time-item">
                <div class="staff-attendance-list-header-break_time">
                    <label class="staff-attendance-list-header-break_time-label">休憩</label>
                </div>
            </th>
            <th class="staff-attendance-list-header-total_hours-item">
                <div class="staff-attendance-list-header-total_hours">
                    <label class="staff-attendance-list-header-total_hours-label">合計</label>
                </div>
            </th>
            <th class="staff-attendance-list-header-detail-item">
                <div class="staff-attendance-list-header-detail">
                    <label class="staff-attendance-list-header-detail-label">詳細</label>
                </div>
            </th>
        </tr>
        @foreach ($dates as $displayDate => $dateObj)
        <tr class="staff-attendance-list-row">
            <td class="staff-attendance-list-date-item">
                <div class="staff-attendance-list-date">
                    <label class="staff-attendance-list-date-label">
                        @php
                        $days = ['日', '月', '火', '水', '木', '金', '土'];
                        $date = \Carbon\Carbon::parse($displayDate);
                        $attendance = $attendances->get($displayDate);
                        @endphp
                        {{ $dateObj->format('m/d') }}({{ $days[$dateObj->dayOfWeek] }})
                    </label>
                </div>
            </td>
            @if($attendance)
            <td class="staff-attendance-list-start_time-item">
                <div class="staff-attendance-list-start_time">
                    <label class="staff-attendance-list-start_time-label">{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</label>
                </div>
            </td>
            <td class="staff-attendance-list-end_time-item">
                <div class="staff-attendance-list-end_time">
                    <label class="staff-attendance-list-end_time-label">{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</label>
                </div>
            </td>
            <td class="staff-attendance-list-break_time-item">
                <div class="staff-attendance-list-break_time">
                    <label class="staff-attendance-list-break_time-label">
                        @php
                        $totalSeconds = 0;
                        foreach($attendance->breaks as $break) {
                        if($break->start_time && $break->end_time) {
                        $start = \Carbon\Carbon::parse($break->start_time)->second(0);
                        $end = \Carbon\Carbon::parse($break->end_time)->second(0);
                        $totalSeconds += $start->diffInSeconds($end);
                        }
                        }

                        $workTime = 0;
                        if($attendance->start_time && $attendance->end_time) {
                        $a_start = \Carbon\Carbon::parse($attendance->start_time)->second(0);
                        $a_end = \Carbon\Carbon::parse($attendance->end_time)->second(0);

                        $workTime = $a_start->diffInSeconds($a_end) - $totalSeconds;
                        }

                        $w_hours = floor($workTime / 3600);
                        $w_minutes = floor(($workTime % 3600) / 60);

                        $totalMinutes = (int)($totalSeconds / 60);
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        @endphp
                        {{ $totalMinutes > 0 ? sprintf('%d:%02d', $hours, $minutes) : '00:00' }}
                    </label>
                </div>
            </td>
            <td class="staff-attendance-list-total_hours-item">
                <div class="staff-attendance-list-total_hours">
                    <label class="staff-attendance-list-total_hours-label">{{ sprintf('%d:%02d', $w_hours, $w_minutes) }}</label>
            </td>
            <td class="staff-attendance-list-detail-item">
                <div class="staff-attendance-list-detail-link">
                    <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id]) }}" class="staff-attendance-list-detail-link-inner">詳細</a>
                </div>
            </td>
            @else
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="staff-attendance-list-detail-link">
                <a href="{{ route('admin.attendance.detail', ['id' => $displayDate, 'user_id' => $user->id]) }}" class="staff-attendance-list-detail-link-inner">詳細</a>
            </td>
            @endif
        </tr>
        @endforeach
    </table>
    <div class="CSV-download-button">
        <button class="CSV-download-button-submit" onclick="location.href='{{ route('admin.staff.csv', ['id' => $user->id, 'month' => $date->format('Y-m')]) }}'" type="button">CSV出力</button>
    </div>
</div>
@endsection