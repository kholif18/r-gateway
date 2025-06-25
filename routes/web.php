<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsappLoginController;

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/whatsapp/send-message', [MessageController::class, 'index'])->name('whatsapp.message');
    Route::post('/whatsapp/send-message', [MessageController::class, 'send'])->name('whatsapp.message.send');

    Route::get('/user', [HistoryController::class, 'user'])->name('user');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');

    Route::get('/report', [ReportController::class, 'index'])->name('report');

    Route::post('/webhook/session', [WebhookController::class, 'session']);
    Route::post('/webhook/message', [WebhookController::class, 'message']);

    Route::prefix('whatsapp')->group(function () {
        Route::get('/login', [WhatsappLoginController::class, 'index'])->name('login.whatsapp');
        Route::get('/status', [WhatsappLoginController::class, 'status'])->name('whatsapp.status');
        Route::get('/qr', [WhatsappLoginController::class, 'qr'])->name('whatsapp.qr');
        Route::post('/logout', [WhatsappLoginController::class, 'logout'])->name('whatsapp.logout');
    });

    Route::get('/whatsapp/start', [WhatsappLoginController::class, 'start'])->name('whatsapp.start');


    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/save', [SettingsController::class, 'save']);
    Route::post('/settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

    Route::get('/profile', [UserController::class, 'edit'])->middleware(['auth'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
});


require __DIR__.'/auth.php';
