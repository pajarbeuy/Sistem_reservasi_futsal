<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FieldController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
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

// Fields CRUD - Public GET, Protected POST/PUT/DELETE
Route::group([
    'prefix' => 'fields'
], function () {
    Route::get('/', [FieldController::class, 'index']);
    Route::get('/{field}', [FieldController::class, 'show']);
    
    // Admin only operations
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [FieldController::class, 'store']);
        Route::put('/{field}', [FieldController::class, 'update']);
        Route::delete('/{field}', [FieldController::class, 'destroy']);
    });
});

// Prices - Public GET, Protected POST/PUT/DELETE
Route::group([
    'prefix' => 'prices'
], function () {
    Route::get('/', [PriceController::class, 'index']);
    Route::get('/{price}', [PriceController::class, 'show']);
    
    // Admin only operations
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [PriceController::class, 'store']);
        Route::put('/{price}', [PriceController::class, 'update']);
        Route::delete('/{price}', [PriceController::class, 'destroy']);
    });
});

// Schedule - Public GET available slots (no auth required)
Route::group([
    'prefix' => 'schedule',
    'middleware' => 'api'
], function () {
    Route::get('/available-slots', [ScheduleController::class, 'getAvailableSlots']);
    Route::get('/day-schedule', [ScheduleController::class, 'getDaySchedule']);
});

// Bookings (web session or JWT auth required)
Route::group([
    'middleware' => [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        'auth:web,api',
    ],
    'prefix' => 'bookings'
], function () {
    Route::get('/', [\App\Http\Controllers\Api\BookingController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\BookingController::class, 'show']);
    Route::post('/{id}/cancel', [\App\Http\Controllers\Api\BookingController::class, 'cancel']);
});

// Payments (web session or JWT auth required)
Route::group([
    'middleware' => [
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        'auth:web,api',
    ],
    'prefix' => 'payments'
], function () {
    Route::post('/', [\App\Http\Controllers\Api\PaymentController::class, 'store']);
    Route::post('/create-midtrans-token', [\App\Http\Controllers\MidtransPaymentController::class, 'createMidtransToken']);
    Route::get('/{id}', [\App\Http\Controllers\Api\PaymentController::class, 'show']);
});

// Midtrans Callback
Route::post('/payments/callback', [\App\Http\Controllers\MidtransPaymentController::class, 'handleCallback'])
    ->name('api.payments.callback');

// Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('api.verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth:api', 'throttle:6,1'])
    ->name('api.verification.resend');
