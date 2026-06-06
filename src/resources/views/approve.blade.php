@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/approve.css') }}">
@endsection

@section('content')
<div class="admin-approve-attendance-content">
    <h2 class="admin-approve-attendance-title">勤怠詳細</h2>
    <form action="{{ route('attendance.request.approve', ['id' => $attendance->id]) }}" method="post">
        @csrf
        <table class="admin-approve-attendance-table">
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-user-name-item">
                    <div class="admin-approve-attendance-header-user-name">
                        <label class="admin-approve-attendance-header-user-name-label">名前</label>
                    </div>
                </th>
                <td class="admin-approve-attendance-user-name-item">
                    <div class="admin-approve-attendance-user-name">
                        <label class="admin-approve-attendance-user-name-label">{{ $attendance->user->name }}</label>
                    </div>
                </td>
            </tr>
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-date-item">
                    <div class="admin-approve-attendance-header-date">
                        <label class="admin-approve-attendance-header-date-label">日付</label>
                    </div>
                </th>
                <td class="admin-approve-attendance-date-item">
                    <div class="admin-approve-attendance-date">
                        <label class="admin-approve-attendance-date-year-label">{{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}</label>
                        <label class="admin-approve-attendance-date-month-label">{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</label>
                    </div>
                </td>
            </tr>
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-start_time-end_time-item">
                    <div class="admin-approve-attendance-header-start_time-end_time">
                        <label class="admin-approve-attendance-header-start_time-end_time-label">出勤・退勤</label>
                    </div>
                </th>
                <td class="admin-approve-attendance-start_time-end_time-item">
                    <div class="admin-approve-attendance-group">
                        <label class="admin-approve-attendance-start_time-label">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</label>
                        <label class="admin-approve-attendance-hyphen">~</label>
                        <label class="admin-approve-attendance-end_time-label">{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}</label>
                        <input type="hidden" name="id" value="{{ $attendance->id }}">
                    </div>
                </td>
            </tr>
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-break-time-item">
                    <div class="admin-approve-attendance-header-break-time">
                        <label class="admin-approve-attendance-header-break-time-label">休憩</label>
                    </div>
                </th>
                <td class="admin-approve-attendance-break-time-item">
                    <div class="admin-approve-attendance-group">
                        <label class="admin-approve-attendance-break1_start_time-label">{{ \Carbon\Carbon::parse($attendance->break1_start_time)->format('H:i') }}</label>
                        <label class="admin-approve-attendance-hyphen">~</label>
                        <label class="admin-approve-attendance-break1_end_time-label">{{ \Carbon\Carbon::parse($attendance->break1_end_time)->format('H:i') }}</label>
                    </div>
                </td>
            </tr>
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-break-time-2-item">
                    <div class="admin-approve-attendance-header-break-time-2">
                        <label class="admin-approve-attendance-header-break-time-2-label">休憩2</label>
                    </div>
                </th>
                <td class="admin-approve-attendance-break-time-2-item">
                    @if($attendance->break2_start_time)
                    <div class="admin-approve-attendance-group">
                        <label class="admin-approve-attendance-break2_start_time-label">{{ \Carbon\Carbon::parse($attendance->break2_start_time)->format('H:i') }}</label>
                        <label class="admin-approve-attendance-hyphen">~</label>
                        <label class="admin-approve-attendance-break2_end_time-label">{{ \Carbon\Carbon::parse($attendance->break2_end_time)->format('H:i') }}</label>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="admin-approve-attendance-table-row">
                <th class="admin-approve-attendance-header-remark-item">
                    <div class="admin-approve-attendance-header-remark">
                        <label class="admin-approve-attendance-header-remark-label">備考</label>
                    </div>
                </th>
                <td class="admin-detail-attendance-remark-item">
                    <div class="admin-approve-attendance-remark">
                        <label class="admin-approve-attendance-remark-label">{{ $attendance->remark }}</label>
                    </div>
                </td>
            </tr>
        </table>
        <div class="admin-approve-attendance-button">
            @if($attendance->status == 1)
            <button class="admin-approved-attendance-button" type="submit" disabled>承認済み</button>
            @else
            <button class="admin-approve-attendance-button-submit" type="submit">承認</button>
            @endif
        </div>
    </form>
</div>
@endsection