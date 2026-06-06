<?php

namespace Tests\Feature;

use Tests\TestCase;

class Verify_EmailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //メール認証機能
    public function testVerify_Email()
    {
        //通知を偽装にして、実際にはメールが送信されないようにする
        \Illuminate\Support\Facades\Notification::fake();

        //会員登録のpostリクエストを送信して、ユーザーを作成する
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'verify-email@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertSessionHasNoErrors();//エラーがないことを確認

        //usersテーブルにユーザーデータが仮登録されていることを確認
        $user = \App\Models\User::where('email', 'verify-email@example.com')->first();
        $this->assertNotNull($user);

        //Laravel標準のメール認証通知が送信されていることを確認
        \Illuminate\Support\Facades\Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);

        //メール認証誘導画面で「認証はこちらから」ボタンを押すとメール認証サイトにアクセスできることを確認
        $response1 = $this->actingAs($user)->get('/verify-email');
        $response1->assertStatus(200);
        $response1->assertSee('認証はこちらから'); //メール認証誘導画面に「認証はこちらから」ボタンが表示されていることを確認

        //メール認証サイトにアクセスすると、メール認証が完了して、勤怠登録画面(/attendance)にリダイレクトされることを確認
        $verrificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $response2 = $this->actingAs($user)->get($verrificationUrl);
        $response2->assertRedirect('/attendance');

        //メール認証が完了していることを確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
