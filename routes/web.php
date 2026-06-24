<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

use App\Models\Field;
use App\Models\Price;
use App\Models\Booking;

Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard - Hanya riwayat booking & transaksi
    Route::get('/dashboard', function () {
        return Inertia::render('UserDashboard', [
            'bookings' => Booking::where('user_id', auth()->id())->with('field')->orderBy('created_at', 'desc')->get(),
            'payments' => \App\Models\Payment::whereHas('booking', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('created_at', 'desc')->get()
        ]);
    })->name('dashboard');

    // Admin Dashboard - Akses semua fitur
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return Inertia::render('Dashboard', [
                'fields' => Field::all(),
                'prices' => Price::all(),
                'bookings' => Booking::with(['user', 'field'])->orderBy('created_at', 'desc')->get()
            ]);
        })->name('admin.dashboard');

        // Field CRUD
        Route::post('/dashboard/fields', function (\Illuminate\Http\Request $request) {
            Field::create($request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'price_per_hour' => 'required|numeric',
                'is_available' => 'boolean'
            ]));
            return back();
        });
        
        Route::put('/dashboard/fields/{field}', function (\Illuminate\Http\Request $request, Field $field) {
            $field->update($request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'price_per_hour' => 'required|numeric',
                'is_available' => 'boolean'
            ]));
            return back();
        });

        Route::delete('/dashboard/fields/{field}', function (Field $field) {
            $field->delete();
            return back();
        });

        // Price CRUD
        Route::post('/dashboard/prices', function (\Illuminate\Http\Request $request) {
            Price::create($request->validate([
                'time_period' => 'required|string',
                'start_time' => 'required',
                'end_time' => 'required',
                'price_per_hour' => 'required|numeric',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]));
            return back();
        });

        Route::put('/dashboard/prices/{price}', function (\Illuminate\Http\Request $request, Price $price) {
            $price->update($request->validate([
                'time_period' => 'required|string',
                'start_time' => 'required',
                'end_time' => 'required',
                'price_per_hour' => 'required|numeric',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]));
            return back();
        });

        Route::delete('/dashboard/prices/{price}', function (Price $price) {
            $price->delete();
            return back();
        });
    }); // Closing the admin middleware group
}); // Closing the outer middleware group

// Public pages
Route::get('/jadwal', function () {
    return Inertia::render('Jadwal');
});

Route::get('/harga', function () {
    return Inertia::render('Harga');
});

Route::get('/lapangan', function () {
    return Inertia::render('Lapangan');
});

// Booking Detail Page - untuk melakukan booking dengan Midtrans payment
Route::get('/lapangan/{id}/booking', function ($id) {
    return Inertia::render('LapanganDetail', ['fieldId' => $id]);
})->middleware('auth')->name('lapangan.booking');

// Midtrans Payment Callbacks
Route::post('/payments/midtrans-callback', [\App\Http\Controllers\MidtransPaymentController::class, 'handleCallback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class])
    ->name('payment.callback');

Route::get('/payment/finish', [\App\Http\Controllers\MidtransPaymentController::class, 'paymentFinish'])
    ->name('payment.finish');

Route::get('/payment/error', [\App\Http\Controllers\MidtransPaymentController::class, 'paymentError'])
    ->name('payment.error');

Route::get('/payment/pending', [\App\Http\Controllers\MidtransPaymentController::class, 'paymentPending'])
    ->name('payment.pending');

Route::get('/tentang', function () {
    return Inertia::render('Tentang');
});

Route::get('/booking-form', function () {
    return Inertia::render('BookingForm');
})->middleware(['auth', 'verified'])->name('booking.form');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
// Auth Routes
