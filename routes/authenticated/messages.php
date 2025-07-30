<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessagesController;

Route::name('messages.')->group(function () {
    Route::get('/messages', [MessagesController::class, 'index'])->middleware('permission:message.index')->name('index');
    Route::get('/messages/create', [MessagesController::class, 'create'])->middleware('permission:message.create')->name('create');
    Route::get('/messages/create?to={user}', [MessagesController::class, 'create'])->middleware('permission:message.create')->name('create-to');
    Route::get('/messages/create?reply_to={message?}', [MessagesController::class, 'create'])->middleware('permission:message.create')->name('reply');
    Route::get('/messages/attachments/{attachment}', [MessagesController::class, 'downloadAttachment'])->middleware('permission:message.view')->name('download-attachment');
    Route::get('/messages/{message}/attachments', [MessagesController::class, 'downloadAllAttachments'])->middleware('permission:message.view')->name('download-all-attachments');
    Route::post('/messages', [MessagesController::class, 'store'])->middleware('permission:message.create')->name('store');
    Route::get('/messages/{message}', [MessagesController::class, 'show'])->middleware('permission:message.view')->name('show');
    Route::get('/messages/{message}/edit', [MessagesController::class, 'edit'])->middleware('permission:message.edit')->name('edit');
    Route::post('/messages/{message}', [MessagesController::class, 'update'])->middleware('permission:message.edit')->name('update');
    Route::delete('/messages/{message}', [MessagesController::class, 'destroy'])->middleware('permission:message.delete')->name('destroy');
});