<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', function () {
    // Logic untuk login akan ditambahkan di sini
})->name('login.post');

Route::post('/register', function () {
    // Logic untuk register akan ditambahkan di sini
})->name('register.post');

Route::get('/password/request', function () {
    // Halaman lupa password
    return view('auth.forgot-password');
})->name('password.request');
