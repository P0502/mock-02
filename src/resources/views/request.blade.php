@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
<div class="request-attendance-content">
    <h2 class="request-attendance-title">申請一覧</h2>
    <div class="attendance-list-links">
        <div class="request-attendance-link {{ request('tab') != 'approved' ? 'active' : '' }}">
            <a class="request-attendance-link-inner" href="/stamp_correction_request/list?tab=pending">承認待ち</a>
        </div>
        <div class="approve-attendance-link {{ request('tab') == 'approved' ? 'active' : '' }}">
            <a class="approve-attendance-link-inner" href="/stamp_correction_request/list?tab=approved">承認済み</a>
        </div>
    </div>
    <table class="request-attendance-table">
        <tr class="request-attendance-table-row-header">
            <th class="request-attendance-status-header">状態</th>
            <th class="request-attendance-name-header">名前</th>
            <th class="request-attendance-date-header">対象日時</th>
            <th class="request-attendance-reason-header">申請理由</th>
            <th class="request-attendance-request-date-header">申請日時</th>
            <th class="request-attendance-detail-header">詳細</th>
        </tr>
        @foreach ($attendances as $attendance)
        <tr class="request-attendance-table-row">
            <td class="request-attendance-status">
                <label class="request-attendance-status-label">{{ $attendance->status == 0 ? '承認待ち' : '承認済み' }}</label>
            </td>
            <td class="request-attendance-name">
                <label class="request-attendance-name-label">{{ Auth::user()->name }}</label>
            </td>
            <td class="request-attendance-date">
                <label class="request-attendance-date-label">{{ \Carbon\Carbon::parse($attendance->date)->format('Y/m/d') }}</label>
            </td>
            <td class="request-attendance-reason">
                <label class="request-attendance-reason-label">{{ $attendance->remark }}</label>
            </td>
            <td class="request-attendance-request-date">
                <label class="request-attendance-request-date-label">{{ $attendance->created_at->format('Y/m/d') }}</label>
            </td>
            <td class="request-attendance-detail-link">
                <a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}" class="request-attendance-detail-link-inner">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection