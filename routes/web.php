<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsappLoginController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/send-message', [MessageController::class, 'index']);
Route::post('/send', [MessageController::class, 'send']);

Route::get('/history', [HistoryController::class, 'index']);
Route::get('/user', [HistoryController::class, 'user']);

Route::get('/wa-login', [WhatsappLoginController::class, 'index']);
Route::get('/whatsapp/qr', [WhatsappLoginController::class, 'qr']);
Route::get('/whatsapp/status', [WhatsappLoginController::class, 'status']);
Route::post('/whatsapp/logout', function () {
    Http::get(env('WA_GATEWAY_API') . '/instance/default/logout');
    return back()->with('status', 'Berhasil logout dari WhatsApp.');
});

Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings/save', [SettingsController::class, 'save']);
Route::post('/settings/reset', [SettingsController::class, 'reset']);