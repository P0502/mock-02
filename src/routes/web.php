<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/stamp_correction_request/approve/{id}', [AttendanceController::class, 'show'])->name('attendance.request.show');
    Route::post('/stamp_correction_request/approve/{id}', [AttendanceController::class, 'approve'])->name('attendance.request.approve');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::post('/attendance/break/start', [AttendanceController::class, 'breakstart'])->name('break.start');
    Route::post('/attendance/break/end', [AttendanceController::class, 'breakend'])->name('break.end');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id?}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/detail/{id?}', [AttendanceController::class, 'store'])->name('attendance.detail');
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'request']);
    Route::get('/admin/attendance/list', [AttendanceController::class, 'adminlist'])->name('admin.attendance-list');
    Route::get('/admin/attendance/{id}', [AttendanceController::class, 'admindetail'])->name('admin.attendance.detail');
    Route::patch('/admin/attendance/{id}', [AttendanceController::class, 'update'])->name('admin.attendance.detail');
    Route::get('/admin/staff/list', [AttendanceController::class, 'stafflist'])->name('admin.staff-list');
    Route::get('/admin/attendance/staff/{id}', [AttendanceController::class, 'staff'])->name('admin.staff');
    Route::get('/admin/attendance/staff/{id}/csv', [AttendanceController::class, 'csv'])->name('admin.staff.csv');
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'requestsRouter'])->name('attendance.request');
});

Route::get('/attendance', [AttendanceController::class, 'index'])->middleware(['auth', 'verified']);

Route::get('admin/login', function () {
    return view('admin.login');
})->name('admin.login');
Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);



Route::get('/verify-email', function () {
    return view('verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/mail/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/verify-email', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back();
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');