<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    //ログイン認証機能(一般ユーザー)
    public function testLogin_user()
    {
        //ログイン画面のURL
        $loginUrl = '/login';

        //メールアドレスが未入力の場合、エラーになることを確認
        $response1 = $this->post($loginUrl, [
            'email' => '',
            'password' => 'password'
        ]);
        $response1->assertSessionHasErrors('email');

        //パスワードが未入力の場合、エラーになることを確認
        $response2 = $this->post($loginUrl, [
            'email' => 'test@example.com',
            'password' => ''
        ]);
        $response2->assertSessionHasErrors('password');

        //登録内容と一致しない場合、エラーになることを確認
        $user = User::factory()->create([
            'email' => 'registertest@example.com',
            'password' => 'password'
        ]);
        $response3 = $this->post($loginUrl, [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);
        $response3->assertSessionHasErrors('email');
    }

    //ログイン認証機能(管理者)
    public function testLogin_admin()
    {
        //ログイン画面のURL
        $loginUrl = '/admin/login';

        //メールアドレスが未入力の場合、エラーになることを確認
        $response1 = $this->post($loginUrl, [
            'email' => '',
            'password' => 'password'
        ]);
        $response1->assertSessionHasErrors('email');

        //パスワードが未入力の場合、エラーになることを確認
        $response2 = $this->post($loginUrl, [
            'email' => 'admin@example.com',
            'password' => ''
        ]);
        $response2->assertSessionHasErrors('password');

        //登録内容と一致しない場合、エラーになることを確認
        $adminUser = User::factory()->create([
            'email' => 'testadmin@example.com',
            'password' => 'password'
        ]);
        $response3 = $this->post($loginUrl, [
            'email' => 'admin@example.com',
            'password' => 'wrong_password'
        ]);
        $response3->assertSessionHasErrors('email');
    }
}
