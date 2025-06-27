<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ApiTokenMiddleware;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\WhatsappApiController;

Route::middleware(ApiTokenMiddleware::class, 'rate.client')->group(function () {
    Route::get('/send', [WhatsappApiController::class, 'send']);
    Route::post('/send', [WhatsappApiController::class, 'send']);

    // Route::get('/api/gateway-status', [DashboardController::class, 'checkStatus']);
});