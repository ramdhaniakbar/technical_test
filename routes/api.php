<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'signin']);
Route::post('/signup', [AuthController::class, 'signup']);

Route::group(['middleware' => 'jwt.auth'], function () {
   Route::get('/check-token', [AuthController::class, 'checkToken']);

   Route::post('/attendance', [AttendanceController::class, 'insertAttendance']);
   Route::get('/attendance', [AttendanceController::class, 'getDataAttendance']);

   Route::post('/attendance/{id}/approve', [AttendanceController::class, 'isApprove']);

   Route::post('/logout', [AuthController::class, 'logout']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
