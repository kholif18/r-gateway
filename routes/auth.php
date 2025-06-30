<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::middleware('guest')->group(function () {

    // 🔐 Auth
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // 📝 Register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // 🔄 Reset Password (Email & WhatsApp)
    Route::prefix('password')->group(function () {
        Route::get('forgot', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot', [PasswordResetLinkController::class, 'store'])->name('password.email');

        Route::get('reset/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset', [NewPasswordController::class, 'store'])->name('password.store');

        // WA OTP Flow
        Route::get('otp', [OtpController::class, 'show'])->name('password.otp');
        Route::post('otp', [OtpController::class, 'verify'])->name('password.otp.verify');
        Route::get('otp/resend', [OtpController::class, 'resendOtp'])->name('password.otp.resend');
        Route::get('wa-reset-form', [OtpController::class, 'resetForm'])->name('password.wa.form');
        Route::post('wa-reset', [OtpController::class, 'resetPassword'])->name('password.wa.reset');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
