<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\UpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Breaks;
use App\Models\AttendanceCorrectRequest;
use App\Models\User;

class AttendanceController extends Controller
{
    public function index()
    {
        Carbon::setLocale('ja');

        $now = Carbon::now();
        $date = $now->isoFormat('YYYY年M月D日(ddd)');
        $time = $now->format('H:i');

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $now->format('Y-m-d'))->first();

        $status = 'before_work';

        if ($attendance) {
            if ($attendance->end_time) {
                $status = 'after_work';
            } else {
                $latestBreak = Breaks::where('attendance_id', $attendance->id)->latest()->first();

                if ($latestBreak && !$latestBreak->end_time) {
                    $status = 'break';
                } else {
                    $status = 'working';
                }
            }
        }

        $after_work = $attendance && $attendance->end_time;

        return view('attendance', compact('date', 'time', 'status', 'after_work'));
    }

    public function start()
    {
        $now = Carbon::now();

        $exists = Attendance::where('user_id', Auth::id())
            ->where('date', now()->format('Y-m-d'))
            ->exists();

        if ($exists) {
            return redirect()->back();
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $now->format('Y-m-d'),
            'start_time' => $now->format('H:i:s')
        ]);

        return redirect()->back()->with('status', 'working');
    }

    public function end()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->whereNull('end_time')->first();

        if ($attendance) {
            $attendance->update([
                'end_time' => $now->format('H:i:s')
            ]);
        }

        return redirect()->back()->with('status', 'after_work');
    }

    public function breakstart()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if ($attendance) {
            Breaks::create([
                'attendance_id' => $attendance->id,
                'start_time' => $now->format('H:i:s')
            ]);
        }

        return redirect()->back()->with('status', 'break');
    }

    public function breakend()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $today)->first();

        if ($attendance) {
            $break = Breaks::where('attendance_id', $attendance->id)->whereNull('end_time')->latest()->first();
        }

        if ($break) {
            $break->update([
                'end_time' => $now->format('H:i:s')
            ]);
        }

        return redirect()->back()->with('status', 'working');
    }

    public function list(Request $request)
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $date = Carbon::parse($month);

        $dates = [];
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        for ($d = $startDate; $d->lte($endDate); $d->addDay()) {
            $dates[$d->format('Y-m-d')] = $d->copy();
        }

        $attendances = Attendance::with('breaks')
        ->where('user_id', Auth::id())
        ->whereYear('date', $date->year)
        ->whereMonth('date', $date->month)
        ->orderBy('date', 'desc')
        ->get()
        ->keyBy('date');

        foreach ($dates as $key => $value) {
            if (isset($attendances[$key])) {
                $dates[$key] = $attendances[$key];
            }
        }

        return view('list', [
            'attendances' => $dates,
            'date' => $date,
        ]);
    }

    public function detail (Request $request, $id = null)
    {
        $target = $id ?? $request->query('date') ?? Carbon::now()->format('Y-m-d');

        if (str_contains($target, '-')) {
            $date = $target;
            $userId = Auth::id();
            $attendance = Attendance::with('breaks')
            ->where('user_id', $userId)
            ->where('date', $date)
            ->first();
        } else {
            $attendance = Attendance::with('breaks')->findOrFail($id);
            $date = $attendance->date;
        }

        $userId = Auth::id();
        $attendanceCorrectRequest = AttendanceCorrectRequest::where('user_id', $userId)
        ->where('date', $date)
        ->first();

        $break = $attendance ? $attendance->breaks->first() : null;
        $user = Auth::user();

        return view('detail', compact('attendance', 'break', 'user', 'date', 'attendanceCorrectRequest'));
    }
    
    public function store(AttendanceRequest $request)
    {
        $userId = Auth::id();

        $attendance = Attendance::where('user_id', $userId)
                    ->where('user_id', Auth::id())
                    ->where('date', $request->date)
                    ->first();

        AttendanceCorrectRequest::create([
            'user_id' => $userId,
            'attendance_id' => $attendance?->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break1_start_time' => $request->break1_start_time,
            'break1_end_time' => $request->break1_end_time,
            'break2_start_time' => $request->break2_start_time,
            'break2_end_time' => $request->break2_end_time,
            'remark' => $request->remark,
            'status' => 0
        ]);

        return redirect()->back();
    }

    public function request (Request $request)
    {
        $tab = $request->query('tab', 'pending');

        if ($tab === 'approved') {
            $attendances = AttendanceCorrectRequest::where('user_id', Auth::id())
            ->where('status', 1)->get();
        } else {
            $attendances = AttendanceCorrectRequest::where('user_id', Auth::id())
            ->where('status', 0)
            ->get();
        }

        return view('request', compact('attendances', 'tab'));
    }

    public function adminlist (Request $request)
    {
        $targetDate = $request->query('date', Carbon::today()->format('Y-m-d'));
        $date = Carbon::parse($targetDate);

        $attendances = Attendance::with(['user', 'breaks'])
        ->whereDate('date', $targetDate)
        ->get();

        return view('admin-list', [
            'attendances' => $attendances, 
            'date' => $date, 
            'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $date->copy()->addDay()->format('Y-m-d')]);
    }

    public function admindetail ($id, Request $request)
    {
        if (str_contains($id, '-')) {

        $date = $id;
        $attendance = null;

        $userId = $request->query('user_id');
        $user = User::findOrFail($userId);

        $attendance = Attendance::with(['user', 'breaks'])
        ->where('user_id', $userId)
        ->where('date', $date)
        ->first();

        $userId = $request->query('user_id');
        $user = User::findOrFail($userId);

        $attendanceCorrectRequest = AttendanceCorrectRequest::where('user_id', $user->id)
            ->where('date', $date)
            ->first();
    } else {
        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);
        $user = $attendance->user;
        $date = $attendance->date;

        $attendanceCorrectRequest = AttendanceCorrectRequest::where('user_id', $user->id)
                ->where('date', $date)
                ->first();
    }

        return view('admin-detail', compact('attendance', 'user', 'date', 'attendanceCorrectRequest', 'id'));
    }

    public function update (UpdateRequest $request)
    {
        $attendance = Attendance::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $request->date],
            [
                'start_time' => $request->attendance_start_time,
                'end_time' => $request->attendance_end_time,
                'remark' => $request->remark
            ]
        );

        if ($request->filled('break1_start_time')) {
            $attendance->breaks()->updateOrCreate(
                ['id' => $request->break1_id],
                [
                    'start_time' => $request->break1_start_time,
                    'end_time' => $request->break1_end_time
                ]
            );
        }

        if ($request->filled('break2_start_time')) {
            $attendance->breaks()->updateOrCreate(
                ['id' => $request->break2_id],
                [
                    'start_time' => $request->break2_start_time,
                    'end_time' => $request->break2_end_time
                ]
            );
        }
        return redirect()->back();
    }

    public function adminrequest (Request $request)
    {
        $tab = $request->query('tab', 'pending');

        if ($tab === 'approved') {
            $requests = AttendanceCorrectRequest::where('status', 1)
            ->with(['user', 'attendance.breaks'])
            ->get();
        } else {
            $requests = AttendanceCorrectRequest::where('status', 0)
            ->with(['user', 'attendance.breaks'])
            ->get();
        }

        return view('admin-request', compact('requests', 'tab'));
    }

    public function requestsRouter(Request $request)
    {
        $user = auth()->user();

        $adminEmails = ['admin@example.com'];

        if (in_array($user->email, $adminEmails)) {
            return $this->adminrequest($request);
        }

        return $this->request($request);
    }

    public function stafflist ()
    {
        $users = User::all();

        return view('staff-list', compact('users'));
    }

    public function staff ($id, Request $request)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $date = Carbon::parse($month);

        $attendances = $user->attendances()
        ->with('breaks')
        ->get()
        ->keyBy('date');

        $dates = [];
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        for ($d = $startDate; $d->lte($endDate); $d->addDay()) {
            $dates[$d->format('Y-m-d')] = $d->copy();
        }

        return view('staff', compact('user', 'attendances', 'date', 'dates'));
    }

    public function csv ($id, Request $request)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $date = Carbon::parse($month);

        $attendances = $user->attendances()
        ->whereMonth('date', $date->month)
        ->whereYear('date', $date->year)
        ->with('breaks')
        ->orderBy('date', 'asc')
        ->get();

        $headers = ['日付', '出勤', '退勤', '休憩開始時間1', '休憩終了時間1', '休憩開始時間2', '休憩終了時間2', '合計'];
        $csvData = implode(',', $headers) . "\n";

        foreach ($attendances as $attendance) {
            $startTime = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
            $endTime = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';
            
            $row = [
                $attendance->date,
                $startTime,
                $endTime
            ];

            for ($i = 0; $i < 2; $i++) {
                $break = $attendance->breaks->get($i);
                $row[] = $break ? Carbon::parse($break->start_time)->format('H:i') : '';
                $row[] = $break ? Carbon::parse($break->end_time)->format('H:i') : '';
            }

            $csvData .= implode(',', $row) . "\n";
        }

        $csvData = mb_convert_encoding($csvData, 'SJIS-win', 'UTF-8');

        $filename = sprintf('%s_%s.csv', $user->name, $month);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function show ($id)
    {
        $attendance = AttendanceCorrectRequest::with('user', 'breaks')->findOrFail($id);

        return view('approve', compact('attendance'));
    }

    public function approve (Request $request, $id)
    {
        $id = $request->route('id');
        $requestData = AttendanceCorrectRequest::find($id);

        $attendance = Attendance::updateOrCreate(
            ['id' => $requestData->attendance_id],
            [
                'user_id' => $requestData->user_id,
                'date' => $requestData->date,
                'start_time' => $requestData->start_time,
                'end_time' => $requestData->end_time
            ]
        );
        
        $attendance->breaks()->delete();

        if ($requestData->break1_start_time) {
                $attendance->breaks()->create([
                    'start_time' => $requestData->break1_start_time,
                    'end_time' => $requestData->break1_end_time
                ]);
            }

        if ($requestData->break2_start_time) {
                $attendance->breaks()->create([
                    'start_time' => $requestData->break2_start_time,
                    'end_time' => $requestData->break2_end_time
                ]);
            }

            $requestData->status = 1;
            $requestData->save();

            return redirect()->back();
    }
}
