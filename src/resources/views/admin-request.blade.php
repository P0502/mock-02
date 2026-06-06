@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-request.css') }}">
@endsection

@section('content')
<div class="admin-request-attendance-list-content">
    <h2 class="admin-request-attendance-list-title">申請一覧</h2>
    <div class="admin-attendance-list-links">
        <div class="admin-request-attendance-link {{ $tab !== 'approved' ? 'active' : '' }}">
            <a class="admin-request-attendance-link-inner" href="{{ route('attendance.request', ['tab' => 'pending']) }}">承認待ち</a>
        </div>
        <div class="admin-approve-attendance-link {{ $tab === 'approved' ? 'active' : '' }}">
            <a class="admin-approve-attendance-link-inner" href="{{ route('attendance.request', ['tab' => 'approved']) }}">承認済み</a>
        </div>
    </div>
    <table class="admin-request-attendance-list-table">
        <tr class="admin-request-attendance-list-table-header-row">
            <th class="admin-request-attendance-list-header-status-item">
                <div class="admin-request-attendance-list-header-status">
                    <label class="admin-request-attendance-list-header-status-label">状態</label>
                </div>
            </th>
            <th class="admin-request-attendance-list-header-name-item">
                <div class="admin-request-attendance-list-header-name">
                    <label class="admin-request-attendance-list-header-name-label">名前</label>
                </div>
            </th>
            <th class="admin-request-attendance-list-header-date-item">
                <div class="admin-request-attendance-list-header-date">
                    <label class="admin-request-attendance-list-header-date-label">対象日時</label>
                </div>
            </th>
            <th class="admin-request-attendance-list-header-reason-item">
                <div class="admin-request-attendance-list-header-reason">
                    <label class="admin-request-attendance-list-header-reason-label">申請理由</label>
                </div>
            </th>
            <th class="admin-request-attendance-list-header-request-date-item">
                <div class="admin-request-attendance-list-header-request-date">
                    <label class="admin-request-attendance-list-header-request-date-label">申請日時</label>
                </div>
            </th>
            <th class="admin-request-attendance-list-header-detail-item">
                <div class="admin-request-attendance-list-header-detail">
                    <label class="admin-request-attendance-list-header-detail-label">詳細</label>
                </div>
            </th>
        </tr>
        @foreach ($requests as $request)
        <tr class="admin-request-attendance-list-table-row">
            <td class="admin-request-attendance-list-status-item">
                <div class="admin-request-attendance-list-status">
                    <label class="admin-request-attendance-list-status-label">{{ $request->status == 1 ? '承認済み' : '承認待ち' }}</label>
                </div>
            </td>
            <td class="admin-request-attendance-list-name-item">
                <div class="admin-request-attendance-list-name">
                    <label class="admin-request-attendance-list-name-label">{{ $request->user->name }}</label>
                </div>
            </td>
            <td class="admin-request-attendance-list-date-item">
                <div class="admin-request-attendance-list-date">
                    <label class="admin-request-attendance-list-date-label">{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</label>
                </div>
            </td>
            <td class="admin-request-attendance-list-reason-item">
                <div class="admin-request-attendance-list-reason">
                    <label class="admin-request-attendance-list-reason-label">{{ $request->remark }}</label>
                </div>
            </td>
            <td class="admin-request-attendance-list-request-date-item">
                <div class="admin-request-attendance-list-request-date">
                    <label class="admin-request-attendance-list-request-date-label">{{ $request->created_at->format('Y/m/d') }}</label>
                </div>
            </td>
            <td class="admin-request-attendance-list-detail-item">
                <div class="admin-request-attendance-list-detail">
                    <a href="{{ route('attendance.request.show', ['id' => $request->id]) }}" class="admin-request-attendance-list-detail-link">詳細</a>
                </div>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection