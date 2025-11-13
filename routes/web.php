<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    })->name('register');

    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', function() {
        return Inertia::render('Auth/ForgotPasswordPage', ['status' => session('status')]);
    })->name('password.request');

    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', function($token, Request $request) {
        return Inertia::render('Auth/ResetPasswordPage', [ 'token' => $token,  'email' => $request->email ]);
    })->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
