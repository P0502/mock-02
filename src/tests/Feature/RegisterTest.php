<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //認証機能(一般ユーザー)
    public function test_register()
    {
        //会員登録画面のURL
        $registerUrl = '/register';

        //名前が未入力の場合、エラーになることを確認
        $response1 = $this->post($registerUrl, [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response1->assertSessionHasErrors('name');

        //メールアドレスが未入力の場合、エラーになることを確認
        $response2 = $this->post($registerUrl, [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response2->assertSessionHasErrors('email');

        //パスワードが未入力の場合、エラーになることを確認
        $response3 = $this->post($registerUrl, [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => ''
        ]);
        $response3->assertSessionHasErrors('password');

        //パスワードが8文字未満の場合、エラーになることを確認
        $response4 = $this->post($registerUrl, [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass'
        ]);
        $response4->assertSessionHasErrors('password');

        //パスワードとパスワード確認が一致しない場合、エラーになることを確認
        $response5 = $this->post($registerUrl, [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password123'
        ]);
        $response5->assertSessionHasErrors('password');

        //正しい入力の場合、ユーザーが作成されることを確認
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];
        $response6 = $this->post($registerUrl, $userData);
        $response6->assertStatus(302); //リダイレクトされることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }
}
