<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::name('notifications.')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsSeen'])->name('markAllRead');
});