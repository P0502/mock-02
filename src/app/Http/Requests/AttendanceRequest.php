<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'start_time' => ['required', 'before:end_time'],
            'end_time' => ['required', 'after:start_time'],
            'break1_start_time' => ['nullable', 'after:start_time', 'before:end_time'],
            'break1_end_time' => ['nullable', 'after:break1_start_time', 'before:end_time'],
            'break2_start_time' => ['nullable', 'after:break1_end_time', 'before:end_time'],
            'break2_end_time' => ['nullable', 'after:break2_start_time', 'before:end_time'],
            'remark' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'start_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.required' => '退勤時間を入力してください',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
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
