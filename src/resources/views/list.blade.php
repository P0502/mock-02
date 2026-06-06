@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="attendance-list-label">勤怠一覧</h2>
    <div class="month-navigation">
        <a href="{{ route('attendance.list', ['month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="month-arrow-navigation-link">←</a>
        <a href="{{ route('attendance.list', ['month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="month-navigation-link">前月</a>
        <span class="month-navigation-current">
            <img src="{{ asset('logo/カレンダー.png') }}" style="width: 20px; height: 23px; vertical-align: middle; margin-right: 5px; margin-bottom: 5px;">
            {{ $date->format('Y/m') }}
        </span>
        <a href="{{ route('attendance.list', ['month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="month-navigation-link">翌月</a>
        <a href="{{ route('attendance.list', ['month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="month-arrow-navigation-link">→</a>
    </div>
    <table class="attendance-list-table">
        <tr class="attendance-list-header">
            <th class="attendance-list-header-date-item">日付</th>
            <th class="attendance-list-header-start_time-item">出勤</th>
            <th class="attendance-list-header-item">退勤</th>
            <th class="attendance-list-header-item">休憩</th>
            <th class="attendance-list-header-item">合計</th>
            <th class="attendance-list-header-detail-item">詳細</th>
        </tr>
        @foreach ($attendances as $displayDate => $attendance)
        <tr class="attendance-list-row">
            <td class="attendance-list-date">
                @php
                $days = ['日', '月', '火', '水', '木', '金', '土'];
                $date = \Carbon\Carbon::parse($displayDate);
                @endphp
                {{ $date->format('m/d') }}({{ $days[$date->dayOfWeek] }})
            </td>
            @if($attendance instanceof App\Models\Attendance)
            <td class="attendance-list-start_time">{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</td>
            <td class="attendance-list-end_time">{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
            <td class="attendance-list-break_time">
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
            </td>
            <td class="attendance-list-total_hours">{{ ($attendance->start_time && $attendance->end_time) ? $attendance->total_working_hours : '' }}</td>
            <td class="attendance-detail-link">
                <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}" class="attendance-detail-link-inner">詳細</a>
            </td>
            @else
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="attendance-detail-link">
                <a href="{{ route('attendance.detail', ['date' => $displayDate]) }}" class="attendance-detail-link-inner">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection