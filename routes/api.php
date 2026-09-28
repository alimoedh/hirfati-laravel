<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AutoReplyController,
    NotificationController,
    PaymentController,
    MessageController,
};

Route::middleware('web')->group(function () {

    Route::post('/auto-reply', [AutoReplyController::class, 'reply']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    Route::post('/payment/create', [PaymentController::class, 'create']);

    Route::get('/messages/{requestId}', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::post('/messages/voice', [MessageController::class, 'storeVoice']);
});
