<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'break1_start_time',
        'break1_end_time',
        'break2_start_time',
        'break2_end_time',
        'status',
        'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function breaks()
    {
        return $this->hasMany(Breaks::class, 'attendance_id');
    }
}
