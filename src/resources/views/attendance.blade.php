@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-content">
    <div class="attendance-panel">
        <div class="attendance-status">
            @if($status === 'before_work')
            <label class="attendance-status-label">勤務外</label>
            @elseif($status === 'working')
            <label class="attendance-status-label">出勤中</label>
            @elseif($status === 'break')
            <label class="attendance-status-label">休憩中</label>
            @elseif($status === 'after_work')
            <label class="attendance-status-label">退勤済</label>
            @endif
        </div>

        <div class="attendance-date">
            <label class="attendance-date-label">{{ $date }}</label>
        </div>
        <div class="attendance-time">
            <label class="attendance-time-label">{{ $time }}</label>
        </div>
        
        <div class="attendance-button-group">
            @php
            $status = session('status') ?? $status;
            @endphp

            @if($status === 'before_work')
            <form action="{{ route('attendance.start') }}" method="post">
                @csrf
                <div class="attendance-start-button">
                    <button type="submit" class="attendance-start-button-submit">出勤</button>
                </div>
            </form>

            @elseif($status === 'working')
            <div class="attendance-button-pair">
                <form action="{{ route('attendance.end') }}" method="post">
                    @csrf
                    <div class="attendance-end-button">
                        <button type="submit" class="attendance-end-button-submit">退勤</button>
                    </div>
                </form>
                <form action="{{ route('break.start') }}" method="post">
                    @csrf
                    <div class="attendance-break-start-button">
                        <button type="submit" class="attendance-break-start-button-submit">休憩入</button>
                    </div>
                </form>
            </div>

            @elseif($status === 'break')
            <div class="attendance-button-pair">
                <form action="{{ route('break.end') }}" method="post">
                    @csrf
                    <div class="attendance-break-end-button">
                        <button type="submit" class="attendance-break-end-button-submit">休憩戻</button>
                    </div>
                </form>
            </div>

            @elseif($status === 'after_work')
            <div class="attendance-after-work-message">
                <label class="attendance-after-work-message-label">お疲れ様でした。</label>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection