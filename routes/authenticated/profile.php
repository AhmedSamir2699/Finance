<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::name('profile.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('update');
});