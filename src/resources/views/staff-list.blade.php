@extends('layouts.admin-app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}">
@endsection

@section('content')
<div class="staff-list">
    <h2 class="staff-list-title">スタッフ一覧</h2>
    <table class="staff-list-table">
        <tr class="staff-list-table-header-row">
            <th class="staff-list-header-name-item">
                <div class="staff-list-header-name">
                    <label class="staff-list-header-name-label">名前</label>
                </div>
            </th>
            <th class="staff-list-header-email-item">
                <div class="staff-list-header-email">
                    <label class="staff-list-header-email-label">メールアドレス</label>
                </div>
            </th>
            <th class="staff-list-header-attendance-detail-item">
                <div class="staff-list-header-attendance-detail">
                    <label class="staff-list-header-attendance-detail-label">月次勤怠</label>
                </div>
            </th>
        </tr>
        @foreach ($users as $user)
        <tr class="staff-list-table-row">
            <td class="staff-list-name-item">
                <div class="staff-list-name">
                    <label class="staff-list-name-label">{{ $user->name }}</label>
                </div>
            </td>
            <td class="staff-list-email-item">
                <div class="staff-list-email">
                    <label class="staff-list-email-label">{{ $user->email }}</label>
                </div>
            </td>
            <td class="staff-list-attendance-detail-item">
                <div class="staff-list-attendance-detail-link">
                    <a href="/admin/attendance/staff/{{ $user->id }}" class="staff-list-attendance-detail-link-inner">詳細</a>
                </div>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection