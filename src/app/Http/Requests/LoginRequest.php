<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(
            function ($validator) {
                $credentials = $this->only('email', 'password');
                if (!Auth::attempt($credentials)) {
                    $validator->errors()->add('email', 'ログイン情報が登録されていません');
                }
            }
        );
    }
}
