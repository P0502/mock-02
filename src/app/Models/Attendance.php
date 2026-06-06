<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(Breaks::class);
    }

    public function getTotalWorkingHoursAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return '0:00';
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        $breakMinites = $this->breaks->sum('duration');

        $totalMinutes = $start->diffInMinutes($end) - $breakMinites;

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getTotalBreakTimeAttribute()
    {
        $totalSeconds = 0;

        foreach ($this->breaks as $break) {
            if ($break->start_time && $break->end_time) {
                $start = Carbon::parse($break->start_time);
                $end = Carbon::parse($break->end_time);
                $totalSeconds += $start->diffInSeconds($end);
            }
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function attendanceCorrectRequest()
    {
        return $this->hasOne(AttendanceCorrectRequest::class);
    }
}
