@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail-attendance-content">
    <h2 class="detail-attendance-title">勤怠詳細</h2>
    <form action="{{ route('attendance.detail', ['id' => $attendance?->id]) }}" method="post">
        @csrf
        <table class="detail-attendance-table">
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-user-name-item">
                    <div class="detail-attendance-header-user-name">
                        <label class="detail-attendance-header-user-name-label">名前</label>
                    </div>
                </th>
                <td class="detail-attendance-user-name-item">
                    <div class="detail-attendance-user-name">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <label class="detail-attendance-user-name-label">{{ $user->name }}</label>
                    </div>
                </td>
            </tr>
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-date-item">
                    <div class="detail-attendance-header-date">
                        <label class="detail-attendance-header-date-label">日付</label>
                    </div>
                </th>
                <td class="detail-attendance-date-item">
                    <div class="detail-attendance-date">
                        <input type="hidden" name="date" id="date" value="{{ $attendance ? $attendance?->date : $date }}">
                        <label class="detail-attendance-date-year-label">{{ \Carbon\Carbon::parse($attendance->date ?? $date)->format('Y年') }}</label>
                        <label class="detail-attendance-date-month-label">{{ \Carbon\Carbon::parse($date)->format('n月j日') }}</label>
                    </div>
                </td>
            </tr>
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-start_time-end_time-item">
                    <div class="detail-attendance-header-start_time-end_time">
                        <label class="detail-attendance-header-start_time-end_time-label">出勤・退勤</label>
                    </div>
                </th>
                <td class="detail-attendance-start_time-end_time">
                    @if ($attendanceCorrectRequest && $attendanceCorrectRequest->status == 0)
                    <div class="request-attendance-start_time-end_time-display">
                        <label class="request-attendance-start_time-label">
                            {{ $attendanceCorrectRequest?->start_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->start_time)->format('H:i') : '' }}
                        </label>
                        <label class="request-attendance-hyphen">~</label>
                        <label class="request-attendance-end_time-label">
                            {{ $attendanceCorrectRequest?->end_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->end_time)->format('H:i') : "" }}
                        </label>
                    </div>
                    @else
                    <div class="detail-attendance-group">
                        <input type="hidden" name="attendance_id" id="attendance_id" value="{{ $attendance?->id }}">
                        <input class="detail-attendance-start_time-input" type="text" name="start_time" id="start_time" value="{{ $attendance?->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}">
                        <label class="detail-attendance-hyphen">~</label>
                        <input class="detail-attendance-end_time-input" type="text" name="end_time" id="end_time" value="{{ $attendance?->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}">
                        <p class="detail-attendance-error">
                            @error('start_time')
                            {{ $message }}
                            @enderror
                            @error('end_time')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-break-time-item">
                    <div class="detail-attendance-header-break-time">
                        <label class="detail-attendance-header-break-time-label">休憩</label>
                    </div>
                </th>
                <td class="detail-attendance-break-time-item">
                    @if ($attendanceCorrectRequest && $attendanceCorrectRequest->status == 0)
                    <div class="request-attendance-break1-time-display">
                        <label class="request-attendance-break1_start_time-label">
                            {{ $attendanceCorrectRequest?->break1_start_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->break1_start_time)->format('H:i') : '' }}
                        </label>
                        <label class="request-attendance-hyphen">~</label>
                        <label class="request-attendance-break1_end_time-label">
                            {{ $attendanceCorrectRequest?->break1_end_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->break1_end_time)->format('H:i') : '' }}
                        </label>
                    </div>
                    @else
                    <div class="detail-attendance-group">
                        <input type="hidden" name="break1_id" id="break1_id" value="{{ $attendance?->breaks->first() ? $attendance->breaks->first()->id : '' }}">
                        <input class="detail-attendance-break-start_time-input" type="text" name="break1_start_time" id="break1_start_time" value="{{ $attendance?->breaks->first() ? \Carbon\Carbon::parse($attendance->breaks->first()->start_time)->format('H:i') : '' }}">
                        <label class="detail-attendance-hyphen">~</label>
                        <input class="detail-attendance-break-end_time-input" type="text" name="break1_end_time" id="break1_end_time" value="{{ $attendance?->breaks->first() ? \Carbon\Carbon::parse($attendance->breaks->first()->end_time)->format('H:i') : '' }}">
                        <p class="detail-attendance-error">
                            @error('break1_start_time')
                            {{ $message }}
                            @enderror
                            @error('break1_end_time')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    @endif
                </td>
            </tr>
            @if ($attendanceCorrectRequest && $attendanceCorrectRequest->status == 0)
            @if (!empty($attendanceCorrectRequest->break2_start_time))
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-break-time-2-item">
                    <div class="detail-attendance-header-break-time-2">
                        <label class="detail-attendance-header-break-time-2-label">休憩2</label>
                    </div>
                </th>
                <td class="detail-attendance-break-time-2-item">
                    <div class="request-attendance-break2-time-display">
                        <label class="request-attendance-break2_start_time-label">
                            {{ $attendanceCorrectRequest?->break2_start_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->break2_start_time)->format('H:i') : '' }}
                        </label>
                        <label class="request-attendance-hyphen">~</label>
                        <label class="request-attendance-break2_end_time-label">
                            {{ $attendanceCorrectRequest?->break2_end_time ? \Carbon\Carbon::parse($attendanceCorrectRequest->break2_end_time)->format('H:i') : '' }}
                        </label>
                    </div>
                </td>
                </th>
            </tr>
            @endif
            @else
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-break-time-2-item">
                    <div class="detail-attendance-header-break-time-2">
                        <label class="detail-attendance-header-break-time-2-label">休憩2</label>
                    </div>
                </th>
                <td class="detail-attendance-break-time-2-item">
                    <div class="detail-attendance-group">
                        <input class="detail-attendance-break-start_time-2-input" type="text" name="break2_start_time" id="break2_start_time" value="{{ $attendance?->breaks->get(1) ? \Carbon\Carbon::parse($attendance->breaks->get(1)->start_time)->format('H:i') : '' }}">
                        <label class="detail-attendance-hyphen">~</label>
                        <input class="detail-attendance-break-end_time-2-input" type="text" name="break2_end_time" id="break2_end_time" value="{{ $attendance?->breaks->get(1) ? \Carbon\Carbon::parse($attendance->breaks->get(1)->end_time)->format('H:i') : '' }}">
                        <input type="hidden" name="break2_id" id="break2_id" value="{{ $attendance?->breaks->get(1)?->id ?? '' }}">
                        <p class="detail-attendance-error">
                            @error('break2_start_time')
                            {{ $message }}
                            @enderror
                            @error('break2_end_time')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                </td>
            </tr>
            @endif
            <tr class="detail-attendance-table-row">
                <th class="detail-attendance-header-remark-item">
                    <div class="detail-attendance-header-remark">
                        <label class="detail-attendance-header-remark-label">備考</label>
                    </div>
                </th>
                <td class="detail-attendance-remark">
                    @if ($attendanceCorrectRequest && $attendanceCorrectRequest->status == 0)
                    <div class="request-attendance-remark-display">
                        <label class="request-attendance-remark-label">
                            {{ $attendanceCorrectRequest?->remark }}
                        </label>
                    </div>
                    @else
                    <div class="detail-attendance-remark">
                        <textarea class="detail-attendance-remark-input" name="remark" id="remark">{{ $attendance?->remark }}</textarea>
                        <p class="detail-attendance-error">
                            @error('remark')
                            {{ $message }}
                            @enderror
                        </p>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
        <div class="update-attendance-button">
            @if ($attendanceCorrectRequest && $attendanceCorrectRequest->status == 0)
            <p class="detail-attendance-request-message">*承認待ちのため修正はできません。</p>
            @else
            <button class="update-attendance-button-submit" type="submit">修正</button>
            @endif
        </div>
    </form>
</div>
@endsection