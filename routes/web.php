<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsappLoginController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/send-message', [MessageController::class, 'index'])->name('message');
    Route::post('/send', [MessageController::class, 'send']);

    Route::get('/user', [HistoryController::class, 'user'])->name('user');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');

    Route::get('/report', [ReportController::class, 'index'])->name('report');

    Route::get('/whatsapp/login', [WhatsappLoginController::class, 'index'])->name('login.whatsapp');
    Route::get('/whatsapp/start', function () {
        $response = Http::get('http://wa-gateway:3000/session/start?session=user_27');
        return $response->json();
    });
    Route::get('/whatsapp/status', function () {
        $response = Http::get('http://wa-gateway:3000/whatsapp/status');
        return $response->json();
    });

    Route::get('/whatsapp/qr', function () {
        $response = Http::get('http://wa-gateway:3000/whatsapp/qr');
        return $response->json();
    });
    Route::post('/whatsapp/logout', [WhatsappLoginController::class, 'logout']);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/save', [SettingsController::class, 'save']);
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

    Route::get('/profile', [UserController::class, 'edit'])->middleware(['auth'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // routes/web.php
    Route::get('/wa/qr', [WhatsAppController::class, 'qr']);
    Route::post('/wa/send', [WhatsAppController::class, 'send']);

    
    Route::get('/wa-login', function () {
        return view('wa-login');
    })->name('wa.login');
});


require __DIR__.'/auth.php';
