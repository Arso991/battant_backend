<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\RolePermissionController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([], function () {
    Route::post('/permissions', [RolePermissionController::class, 'createPermission']);
    Route::post('/roles', [RolePermissionController::class, 'createRole']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/verify_mail/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['signed'])->name('verifyEmail');
    Route::post('/email/resend', [AuthController::class, 'emailResend'])->middleware(['auth:sanctum', 'throttle:6,1'])->name('resendEmail');;
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password/{hash}', [AuthController::class, 'resetPassword']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
});
