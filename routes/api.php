<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/user-profile', [AuthController::class, 'userProfile'])->middleware('auth:api');
});

// Fields CRUD
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'fields'
], function () {
    Route::get('/', [FieldController::class, 'index']);
    Route::get('/{field}', [FieldController::class, 'show']);
    Route::post('/', [FieldController::class, 'store']);
    Route::put('/{field}', [FieldController::class, 'update']);
    Route::delete('/{field}', [FieldController::class, 'destroy']);
});

// Bookings
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'bookings'
], function () {
    Route::get('/', [\App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\BookingController::class, 'show']);
    Route::post('/{id}/cancel', [\App\Http\Controllers\Api\BookingController::class, 'cancel']);
});

// Payments
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'payments'
], function () {
    Route::post('/', [\App\Http\Controllers\Api\PaymentController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'show']);
});

// Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth:api', 'throttle:6,1'])
    ->name('verification.resend');
