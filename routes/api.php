<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\MessageLogController;
use App\Http\Controllers\Api\WhatsappApiController;

Route::middleware(ApiTokenMiddleware::class, 'rate.client')->group(function () {
    Route::get('/send', [WhatsappApiController::class, 'send']);
    Route::post('/send', [WhatsappApiController::class, 'send']);
    Route::post('/wa/send-media', [WhatsappApiController::class, 'sendMedia']);
    Route::post('/wa/send-group', [WhatsappApiController::class, 'sendGroup']);
    Route::get('/logs', [MessageLogController::class, 'api']);
    Route::post('/logs', [MessageLogController::class, 'store']);
});

    Route::post('/check-client', [ClientAuthController::class, 'check']);

