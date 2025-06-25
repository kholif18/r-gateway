<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

/*
|--------------------------------------------------------------------------
| API Routes for WA Gateway
|--------------------------------------------------------------------------
*/

Route::prefix('whatsapp')->group(function () {
    // Status koneksi
    Route::get('status', [WhatsAppController::class, 'getStatus']);
    
    // Mengirim pesan
    Route::post('send', [WhatsAppController::class, 'sendMessage']);
    
    // Mendapatkan QR Code
    Route::get('qr', [WhatsAppController::class, 'getQrCode']);
    
    // Logout session
    Route::post('logout', [WhatsAppController::class, 'logout']);
    
    // Webhook untuk menerima pesan masuk (jika diperlukan)
    Route::post('webhook', [WhatsAppController::class, 'handleWebhook']);
});

// Route untuk testing koneksi
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'WA Gateway API is working',
        'timestamp' => now()->toDateTimeString()
    ]);
});