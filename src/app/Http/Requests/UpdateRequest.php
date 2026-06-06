<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => ['required'],
            'date' => ['required'],
            'attendance_start_time' => ['required', 'before:attendance_end_time'],
            'attendance_end_time' => ['required', 'after:attendance_start_time'],
            'break1_start_time' => ['nullable', 'after:attendance_start_time', 'before:attendance_end_time'],
            'break1_end_time' => ['nullable', 'after:break1_start_time', 'before:attendance_end_time'],
            'break2_start_time' => ['nullable', 'after:break1_end_time', 'before:attendance_end_time'],
            'break2_end_time' => ['nullable', 'after:break2_start_time', 'before:attendance_end_time'],
            'remark' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'attendance_start_time.required' => '出勤時間を入力してください',
            'attendance_start_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'attendance_end_time.required' => '退勤時間を入力してください',
            'attendance_end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break1_start_time.after' => '休憩時間が不適切な値です',
            'break1_start_time.before' => '休憩時間が不適切な値です',
            'break1_end_time.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'break1_end_time.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'break2_start_time.after' => '休憩時間が不適切な値です',
            'break2_start_time.before' => '休憩時間が不適切な値です',
            'break2_end_time.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'break2_end_time.before' => '休憩時間もしくは退勤時間が不適切な値です',
            'remark.required' => '備考を記入してください'
        ];
    }
}
