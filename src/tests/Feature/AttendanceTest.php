<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    //日時取得機能
    public function testAttendance_date()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(\Carbon\Carbon::parse('2026-06-02 09:00:00'));

        //打刻画面のURL
        $url = '/attendance';
        $response = $this->actingAs($this->user)->get($url);
        $response->assertStatus(200);

        //画面内に「2026年6月2日(火)」の文字列が含まれているか検証
        $response->assertSee('2026年6月2日(火)');
    }

    //ステータス機能
    public function testAttendance_status()
    {
        //テストの現在時刻を固定
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //打刻画面のURL
        $url = '/attendance';

        //勤務外の場合、勤怠ステータスが正しく表示されるか確認
        $response1 = $this->actingAs($this->user)->get($url);
        $response1->assertStatus(200);
        $response1->assertSee('勤務外');

        //出勤中の場合、勤怠ステータスが正しく表示されるか確認
        //出勤データの作成
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => null
        ]);

        $response2 = $this->actingAs($this->user)->get($url);
        $response2->assertStatus(200);
        $response2->assertSee('出勤中');

        //休憩中の場合、勤怠ステータスが正しく表示されるか確認
        //breaksテーブルに、休憩終了時間がnullのデータを作成
        \Illuminate\Support\Facades\DB::table('breaks')->insert([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response3 = $this->actingAs($this->user)->get($url);
        $response3->assertStatus(200);
        $response3->assertSee('休憩中');

        //退勤済みの場合、勤怠ステータスが正しく表示されるか確認
        //休憩を終了させ、退勤時間(end_time)を更新する
        \Illuminate\Support\Facades\DB::table('breaks')
        ->where('attendance_id', $attendance->id)
        ->update(['end_time' => '13:00:00']); //休憩終了時間

        $attendance->update(['end_time' => '18:00:00']);

        $response4 = $this->actingAs($this->user)->get($url);
        $response4->assertStatus(200);
        $response4->assertSee('退勤済');
    }

    //出勤時間打刻機能
    public function testAttendance_start_time()
    {
        //出勤ボタンが正しく機能するかのテスト
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //出勤時間を打刻するリクエストを送信
        $response = $this->actingAs($this->user)->post('/attendance/start');
        //打刻後にリダイレクトされることを確認
        $response->assertStatus(302);

        //データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => null
        ]);

        //出勤は一日に一回しか打刻できないことを確認
        //画面でボタンが消えていても、強制的に2回目のリクエストを送信して、エラーになることを確認
        $duplicateResponse = $this->actingAs($this->user)->post('/attendance/start');

        //データベースの該当レコードが1件しか存在しないことを確認
        $this->assertEquals(1, Attendance::where('user_id', $this->user->id)->count());
    }

    //出勤は一日に一回しか打刻できないことを確認する機能
    public function testAttendance_start_time_duplicate()
    {
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //1回目の打刻
        $this->actingAs($this->user)->post('/attendance/start');

        //画面でボタンが消えていても、強制的に2回目のリクエストを送信
        $duplicateResponse = $this->actingAs($this->user)->post('/attendance/start');

        //データベースの該当レコードが1件しか存在しないことを確認
        $this->assertEquals(1, Attendance::where('user_id', $this->user->id)->count());
    }

    //休憩機能
    public function testAttendance_break()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //その日の出勤データがすでに存在する状態を作る
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => null
        ]);

        //URL設計
        $breakStartUrl = "/attendance/break/start";
        $breakEndUrl = "/attendance/break/end";

        //休憩ボタンが正しく機能するか確認
        //休憩開始のリクエストを送信
        $this->travelTo(\Carbon\Carbon::parse('2026-05-17 12:00:00'));
        $response1 = $this->actingAs($this->user)->post($breakStartUrl);
        $response1->assertStatus(302); //リダイレクトされることを確認

        //breaksテーブルにend_timeがnullの休憩データがあることを確認
        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => null
        ]);

        //休憩戻ボタンが正しく機能するか確認
        //休憩終了のリクエストを送信
        $this->travelTo(\Carbon\Carbon::parse('2026-05-17 12:45:00'));
        $response2 = $this->actingAs($this->user)->post($breakEndUrl);
        $response2->assertStatus(302); //リダイレクトされることを確認

        //breaksテーブルにend_timeが更新されたことを確認
        $this->assertDatabaseHas('breaks', [
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '12:45:00'
        ]);

        //休憩は一日に複数回取れることを確認
        //2回目の休憩開始のリクエストを送信
        $this->travelTo(\Carbon\Carbon::parse('2026-05-17 15:00:00'));
        $this->actingAs($this->user)->post($breakStartUrl);

        //2回目の休憩終了のリクエストを送信
        $this->travelTo(\Carbon\Carbon::parse('2026-05-17 15:30:00'));
        $this->actingAs($this->user)->post($breakEndUrl);

        //breaksテーブルに2回目の休憩データが正しく保存されていることを確認
        $this->assertEquals(2, \Illuminate\Support\Facades\DB::table('breaks')->where('attendance_id', $attendance->id)->count());

        //休憩時間が勤怠一覧画面で確認できるか確認
        $viewResponse = $this->actingAs($this->user)->get('/attendance/list');
        $viewResponse->assertStatus(200);

        //画面に休憩した合計時間の文字列が含まれているか検証
        $viewResponse->assertSee('1:15'); //合計休憩時間
    }

    //退勤時間打刻機能
    public function testAttendance_end_time()
    {
        //テスト内の現在時刻
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //時間を進めるコード
        $this->travelTo(Carbon::parse('2026-05-17 18:00:00'));

        //その日の出勤データがすでに存在する状態を作る
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => null
        ]);

        //退勤ボタンを押すリクエストを送信
        $response = $this->actingAs($this->user)->post('/attendance/end');

        //打刻後にリダイレクトされることを確認
        $response->assertStatus(302);

        //データベースのend_timeが正しく更新されていることを確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'end_time' => '18:00:00'
        ]);

        //勤怠一覧画面にアクセスして、出勤・退勤時間が表示されているか確認
        $viewResponse = $this->actingAs($this->user)->get('/attendance/list');
        $viewResponse->assertStatus(200);

        //画面内に指定した時刻の文字列が含まれているか検証
        $viewResponse->assertSee('09:00'); //出勤時間
        $viewResponse->assertSee('18:00'); //退勤時間
    }

    //勤怠一覧情報取得機能(一般ユーザー)
    public function testAttendance_list()
    {
        //テスト内の現在時刻
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //勤怠データを複数作成
        $currentMonthAttendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        $pastMonthAttendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-04-17',
            'start_time' => '08:30:00'        
        ]);

        $nextMonthAttendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2026-06-17',
            'start_time' => '09:15:00'        
        ]);

        //勤怠一覧画面にリダイレクト,自分が行った勤怠情報が全て表示されている
        $response = $this->actingAs($this->user)->get('/attendance/list');
        $response->assertStatus(200);

        //画面に現在の月が表示されていることを確認
        $response->assertSee('2026-05');
        //今月の自分の打刻データ
        $response->assertSee('09:00'); //出勤時間
        $response->assertSee('18:00'); //退勤時間

        //前月を押したときに前月の勤怠データが表示されることを確認
        $pastResponse = $this->actingAs($this->user)->get('/attendance/list?month=2026-04');
        $pastResponse->assertStatus(200);
        $pastResponse->assertSee('2026-04');
        $pastResponse->assertSee('08:30'); //出勤時間

        //翌月を押したときに翌月の勤怠データが表示されることを確認
        $nextResponse = $this->actingAs($this->user)->get('/attendance/list?month=2026-06');
        $nextResponse->assertStatus(200);
        $nextResponse->assertSee('2026-06');
        $nextResponse->assertSee('09:15'); //出勤時間

        //詳細を押すとその日の勤怠詳細画面にリダイレクトされることを確認
        $detailResponse = $this->actingAs($this->user)->get("/attendance/detail/{$currentMonthAttendance->id}");
        $detailResponse->assertStatus(200);
    }

    //勤怠詳細情報取得機能(一般ユーザー)
    public function testAttendance_detail()
    {
        //ユーザー名の設定
        $this->user->update(['name' => 'テストユーザー']);

        //勤怠データの作成
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        //休憩データの挿入
        \Illuminate\Support\Facades\DB::table('breaks')->insert([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //IDを指定したURL/attendance/detail/{id}にGETリクエストを送信
        $response = $this->actingAs($this->user)->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        //勤怠詳細画面にログインしているユーザー名が表示されていることを確認
        $response->assertSee('テストユーザー');

        //勤怠詳細画面の日付が選択した勤怠の日付と一致していることを確認
        $response->assertSee('2026年');
        $response->assertSee('5月17日');

        //勤怠詳細画面に出勤時間、退勤時間が表示されていることを確認
        $response->assertSee('09:00'); //出勤時間
        $response->assertSee('18:00'); //退勤時間

        //勤怠詳細画面に休憩時間が表示されていることを確認
        $response->assertSee('12:00'); //休憩開始時間
        $response->assertSee('13:00'); //休憩終了時間
    }

    //勤怠詳細情報修正機能(一般ユーザー)
    public function testAttendance_detail_update()
    {
        //現在時刻の固定
        $this->travelTo(Carbon::parse('2026-05-17 09:00:00'));

        //既存の勤怠データの作成
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        $updateUrl = "/attendance/detail/{$attendance->id}";

        //出勤時間が退勤時間より後になっている場合、エラーになることを確認
        $response1 = $this->actingAs($this->user)->post($updateUrl, [
            'start_time' => '19:00:00',
            'end_time' => '18:00:00',
            'remark' => 'テスト'
        ]);
        $response1->assertSessionHasErrors(['start_time']);

        //休憩開始時間が退勤時間より後になっている場合、エラーになることを確認
        $response2 = $this->actingAs($this->user)->post($updateUrl, [
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break1_start_time' => '19:00:00',
            'break2_start_time' => '20:00:00',
            'remark' => 'テスト'
        ]);
        $response2->assertSessionHasErrors(['break1_start_time']);
        $response2->assertSessionHasErrors(['break2_start_time']);

        //休憩終了時間が退勤時間より後になっている場合、エラーになることを確認
        $response3 = $this->actingAs($this->user)->post($updateUrl, [
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break1_start_time' => '12:00:00',
            'break1_end_time' => '19:00:00',
            'break2_start_time' => '15:00:00',
            'break2_end_time' => '20:00:00',
            'remark' => 'テスト'
        ]);
        $response3->assertSessionHasErrors(['break1_end_time']);
        $response3->assertSessionHasErrors(['break2_end_time']);

        //備考欄が未入力の場合、エラーになることを確認
        $response4 = $this->actingAs($this->user)->post($updateUrl, [
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break1_start_time' => '12:00:00',
            'break1_end_time' => '13:00:00',
            'remark' => ''
        ]);
        $response4->assertSessionHasErrors(['remark']);

        //修正申請処理が正しく機能することを確認
        $validData = [
            'date' => '2026-05-17',
            'start_time' => '09:30:00',
            'end_time' => '18:30:00',
            'break1_start_time' => '12:30:00',
            'break1_end_time' => '13:30:00',
            'remark' => '修正申請のテスト'
        ];
        $response5 = $this->actingAs($this->user)->post($updateUrl, $validData);
        $response5->assertStatus(302); //リダイレクトされることを確認

        //「承認待ち」にログインユーザーが行った勤怠修正申請が表示されていることを確認
        //「承認済み」に管理者が承認した修正申請が全て表示されていることを確認
        $listResponse = $this->actingAs($this->user)->get('/stamp_correction_request/list');
        $listResponse->assertStatus(200);

        //画面内にそれぞれの状態や申請内容が表示されていることを確認
        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee('修正申請のテスト');
        $listResponse->assertSee('承認済み');

        //各詳細の「詳細」を押すと、勤怠詳細画面にリダイレクトされることを確認
        $detailResponse = $this->actingAs($this->user)->get("/attendance/detail/{$attendance->id}");
        $detailResponse->assertStatus(200);
    }

    //勤怠一覧情報取得機能(管理者)
    public function testAdmin_attendance_list_features()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(Carbon::parse('2026-06-02 09:00:00'));

        //テストデータの作成
        $userA = User::factory()->create(['name' => 'ユーザーA']);
        $userB = User::factory()->create(['name' => 'ユーザーB']);

        //6月2日(当日)のデータ
        Attendance::factory()->create([
            'user_id' => $userA->id,
            'date' => '2026-06-02',
            'start_time' => '09:00:00',
            'end_time' => null
        ]);
        Attendance::factory()->create([
            'user_id' => $userB->id,
            'date' => '2026-06-02',
            'start_time' => '09:30:00',
            'end_time' => null
        ]);

        //6月1日(前日)のデータ
        Attendance::factory()->create([
            'user_id' => $userA->id,
            'date' => '2026-06-01',
            'start_time' => '08:15:00',
            'end_time' => '18:00:00'
        ]);

        //6月3日(翌日)のデータ
        Attendance::factory()->create([
            'user_id' => $userB->id,
            'date' => '2026-06-03',
            'start_time' => '10:45:00',
            'end_time' => null
        ]);

        //管理者用の勤怠一覧画面URL
        $adminListurl = '/admin/attendance/list';

        //管理者として勤怠一覧画面にアクセス
        $response = $this->actingAs($this->user)->get($adminListurl);
        $response->assertStatus(200);

        //現在の日付が表示されていることを確認
        $response->assertSee('2026年6月2日');

        //当日のユーザーAとユーザーBの勤怠情報が表示されていることを確認
        $response->assertSee('ユーザーA');
        $response->assertSee('09:00'); //ユーザーAの出勤時間
        $response->assertSee('ユーザーB');
        $response->assertSee('09:30'); //ユーザーBの出勤時間

        //前日と翌日の勤怠情報が表示されていないことを確認
        $response->assertDontSee('08:15'); //ユーザーAの前日の出勤時間
        $response->assertDontSee('10:45'); //ユーザーBの翌日の出勤時間

        //前日のタブを押したときに、それぞれの勤怠情報が表示されることを確認
        $pastResponse = $this->actingAs($this->user)->get('/admin/attendance/list?date=2026-06-01');
        $pastResponse->assertStatus(200);
        $pastResponse->assertSee('2026年6月1日');
        $pastResponse->assertSee('ユーザーA');
        $pastResponse->assertSee('08:15'); //ユーザーAの前日の出勤時間

        $pastResponse->assertDontSee('09:15'); //当日のデータが見えないこと


        //翌日のタブを押したときに、それぞれの勤怠情報が表示されることを確認
        $nextResponse = $this->actingAs($this->user)->get('/admin/attendance/list?date=2026-06-03');
        $nextResponse->assertStatus(200);
        $nextResponse->assertSee('2026年6月3日');
        $nextResponse->assertSee('ユーザーB');
        $nextResponse->assertSee('10:45'); //ユーザーBの翌日の出勤時間

        $nextResponse->assertDontSee('09:30'); //当日のデータが見えないこと
    }

    //勤怠詳細情報取得・修正機能(管理者)
    public function testAttendance_detail_admin()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(Carbon::parse('2026-06-02 09:00:00'));

        //ユーザーと勤怠データの作成    
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-02',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        //管理者用の勤怠詳細画面URL
        $adminDetailUrl = "/admin/attendance/{$attendance->id}";

        //詳細画面にアクセスして、選択した勤怠情報が表示されていることを確認
        $response = $this->actingAs($this->user)->get($adminDetailUrl);
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('2026-06-02');
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        //出勤時間が退勤時間より後になっている場合、エラーになることを確認
        $response1 = $this->actingAs($this->user)->patch($adminDetailUrl, [
            'attendance_start_time' => '19:00:00',
            'attendance_end_time' => '18:00:00',
            'remark' => 'テスト'
        ]);
        $response1->assertSessionHasErrors(['attendance_start_time']);

        //休憩開始時間が退勤時間より後になっている場合、エラーになることを確認
        $response2 = $this->actingAs($this->user)->patch($adminDetailUrl, [
            'attendance_start_time' => '09:00:00',
            'attendance_end_time' => '18:00:00',
            'break1_start_time' => '19:00:00',
            'break2_start_time' => '20:00:00',
            'remark' => 'テスト'
        ]);
        $response2->assertSessionHasErrors(['break1_start_time']);
        $response2->assertSessionHasErrors(['break2_start_time']);

        //休憩終了時間が退勤時間より後になっている場合、エラーになることを確認
        $response3 = $this->actingAs($this->user)->patch($adminDetailUrl, [
            'attendance_start_time' => '09:00:00',
            'attendance_end_time' => '18:00:00',
            'break1_start_time' => '12:00:00',
            'break1_end_time' => '19:00:00',
            'break2_start_time' => '15:00:00',
            'break2_end_time' => '20:00:00',
            'remark' => 'テスト'
        ]);
        $response3->assertSessionHasErrors(['break1_end_time']);
        $response3->assertSessionHasErrors(['break2_end_time']);

        //備考欄が未入力の場合、エラーになることを確認
        $response4 = $this->actingAs($this->user)->patch($adminDetailUrl, [
            'attendance_start_time' => '09:00:00',
            'attendance_end_time' => '18:00:00',
            'break1_start_time' => '12:00:00',
            'break1_end_time' => '13:00:00',
            'remark' => ''
        ]);
        $response4->assertSessionHasErrors(['remark']);
    }

    //ユーザー情報取得機能(管理者)
    public function testAttendance_staff()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(Carbon::parse('2026-06-02 09:00:00'));

        //一般ユーザー2人の作成と今月、前月、翌月の勤怠データの作成
        $userA = User::factory()->create(['name' => 'ユーザーA', 'email' => 'userA@example.com']);
        $userB = User::factory()->create(['name' => 'ユーザーB', 'email' => 'userB@example.com']);

        //ユーザーAの今月(5月)の勤怠データ
        $currentMonthAttendanceA = Attendance::create([
            'user_id' => $userA->id,
            'date' => '2026-05-17',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        //ユーザーAの前月(4月)の勤怠データ
        Attendance::create([
            'user_id' => $userA->id,
            'date' => '2026-04-17',
            'start_time' => '08:30:00',
            'end_time' => '17:30:00'
        ]);

        //ユーザーAの翌月(6月)の勤怠データ
        Attendance::create([
            'user_id' => $userA->id,
            'date' => '2026-06-17',
            'start_time' => '09:15:00',
            'end_time' => '18:15:00'
        ]);

        //ユーザーBの今月(5月)の勤怠データ
        $currentMonthAttendanceB = Attendance::create([
            'user_id' => $userB->id,
            'date' => '2026-05-17',
            'start_time' => '09:30:00',
            'end_time' => '18:30:00'
        ]);

        //ユーザーBの前月(4月)の勤怠データ
        Attendance::create([
            'user_id' => $userB->id,
            'date' => '2026-04-17',
            'start_time' => '08:45:00',
            'end_time' => '17:45:00'
        ]);

        //ユーザーBの翌月(6月)の勤怠データ
        Attendance::create([
            'user_id' => $userB->id,
            'date' => '2026-06-17',
            'start_time' => '09:15:00',
            'end_time' => '18:15:00'
        ]);

        //管理者用のユーザー情報画面URL
        $adminStaffUrl = '/admin/staff/list';

        //管理者ユーザーが全一般ユーザーの氏名とメールアドレスを取得できることを確認
        $response = $this->actingAs($this->user)->get($adminStaffUrl);
        $response->assertStatus(200);
        $response->assertSee('ユーザーA');
        $response->assertSee('userA@example.com');
        $response->assertSee('ユーザーB');
        $response->assertSee('userB@example.com');

        //ユーザーの勤怠情報が正しく表示されることを確認
        $attendanceResponse = $this->actingAs($this->user)->get("/admin/attendance/staff/{$userA->id}?month=2026-05");
        $attendanceResponse->assertStatus(200);

        //ユーザーAの今月の勤怠情報
        $attendanceResponse->assertSee('2026-05');
        $attendanceResponse->assertSee('09:00'); //出勤時間
        $attendanceResponse->assertSee('18:00'); //退勤時間

        //前月を押したときにユーザーAの前月の勤怠情報が表示されることを確認
        $pastResponse = $this->actingAs($this->user)->get("/admin/attendance/staff/{$userA->id}?month=2026-04");
        $pastResponse->assertStatus(200);
        $pastResponse->assertSee('2026-04');
        $pastResponse->assertSee('08:30'); //出勤時間
        $pastResponse->assertSee('17:30'); //退勤時間

        //翌月を押したときにユーザーAの翌月の勤怠情報が表示されることを確認
        $futureResponse = $this->actingAs($this->user)->get("/admin/attendance/staff/{$userA->id}?month=2026-06");
        $futureResponse->assertStatus(200);
        $futureResponse->assertSee('2026-06');
        $futureResponse->assertSee('09:15'); //出勤時間
        $futureResponse->assertSee('18:15'); //退勤時間

        //詳細を押すとユーザーAのその日の勤怠詳細画面にリダイレクトされることを確認
        $adminDetailUrl = "/admin/attendance/{$currentMonthAttendanceA->id}";
        $detailResponse = $this->actingAs($this->user)->get($adminDetailUrl);
        $detailResponse->assertStatus(200);
    }

    //勤怠情報修正機能(管理者)
    public function testAttendance_update_admin()
    {
        //テスト内の現在時刻を固定
        $this->travelTo(Carbon::parse('2026-06-02 09:00:00'));

        //ユーザーと承認待ちの勤怠データと承認済みの勤怠データの作成
        $user = User::factory()->create(['name' => 'テストユーザー']);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-02',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);

        //修正申請テーブルに承認待ちのデータを作成
        $pendingRequestId = \Illuminate\Support\Facades\DB::table('attendance_correct_requests')->insertGetId([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => '2026-06-02',
            'status' => '0',
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'break1_start_time' => '13:00:00',
            'break1_end_time' => '14:00:00',
            'break2_start_time' => null,
            'break2_end_time' => null,
            'remark' => '修正申請のテスト',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //修正申請テーブルに承認済みのデータを作成
        \Illuminate\Support\Facades\DB::table('attendance_correct_requests')->insertGetId([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => '2026-06-02',
            'status' => '1',
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'break1_start_time' => '13:00:00',
            'break1_end_time' => '14:00:00',
            'break2_start_time' => null,
            'break2_end_time' => null,
            'remark' => '修正申請のテスト',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //管理者用の申請一覧画面URLと申請詳細画面URL
        $adminRequestListUrl = '/stamp_correction_request/list';
        $adminRequestDetailUrl = "/stamp_correction_request/approve/{$pendingRequestId}";

        //管理者ユーザーが申請一覧画面にアクセスして、承認待ちと承認済みの両方の修正申請が表示されていることを確認
        $listResponse = $this->actingAs($this->user)->get($adminRequestListUrl);
        $listResponse->assertStatus(200);
        $listResponse->assertSee('承認待ち');
        $listResponse->assertSee('承認済み');

        //管理者ユーザーが申請詳細画面にアクセスして、修正申請の内容が表示されていることを確認
        $detailResponse = $this->actingAs($this->user)->get($adminRequestDetailUrl);
        $detailResponse->assertStatus(200);

        //詳細画面に申請者の名前、修正したい時間、修正理由が表示されていることを確認
        $detailResponse->assertSee('テストユーザー');
        $detailResponse->assertSee('10:00'); //修正申請の出勤時間
        $detailResponse->assertSee('19:00'); //修正申請の退勤時間
        $detailResponse->assertSee('13:00'); //修正申請の休憩開始時間
        $detailResponse->assertSee('14:00'); //修正申請の休憩終了時間
        $detailResponse->assertSee('修正申請のテスト'); //修正理由

        //管理者が修正申請を承認したときに、勤怠データが修正申請の内容に更新されることを確認
        $approveResponse = $this->actingAs($this->user)->post("/stamp_correction_request/approve/{$pendingRequestId}");
        $approveResponse->assertStatus(302);

        //データベースの勤怠データのステータスが「承認済み」に更新されていることを確認
        $this->assertDatabaseHas('attendance_correct_requests', [
            'id' => $pendingRequestId,
            'status' => '1'
        ]);

        //attendancesテーブルの勤怠データが修正申請の内容に更新されていることを確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00'
        ]);
    }
}
