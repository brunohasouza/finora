<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::resource('transactions', TransactionController::class)->except(['index', 'create', 'show', 'edit']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/categories/list', [CategoryController::class, 'list']);
    Route::resource('categories', CategoryController::class);
    Route::get('/accounts/list', [WalletController::class, 'list']);
    Route::resource('accounts', WalletController::class);
    Route::resource('banks', BankController::class);
});

Route::middleware(['guest'])->group(function() {
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'forgotPasswordPage'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'resetPasswordPage'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
});
