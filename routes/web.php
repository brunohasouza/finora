<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/forgot-password', 'Auth/ForgotPasswordPage');
Route::inertia('/reset-password', 'Auth/ResetPasswordPage');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return Inertia::render('HomePage');
    })->name('home');

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['guest'])->group(function() {
    Route::get('/login', function() {
        return Inertia::render('Auth/LoginPage');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', function() {
        return Inertia::render('Auth/RegisterPage');
    });

    Route::post('/register', [AuthController::class, 'register']);
});
