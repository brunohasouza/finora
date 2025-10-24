<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'HomePage');
Route::inertia('/login', 'Auth/LoginPage');
Route::inertia('/register', 'Auth/RegisterPage');
Route::post('/register', [AuthController::class, 'register']);
Route::inertia('/forgot-password', 'Auth/ForgotPasswordPage');
Route::inertia('/reset-password', 'Auth/ResetPasswordPage');
