<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EvaluateController;

Route::name('evaluate.')->group(function () {
    Route::get('/evaluate/random', [EvaluateController::class, 'random'])->name('random')->middleware('permission:evaluate.create');
    Route::get('/evaluate', [EvaluateController::class, 'index'])->name('index')->middleware('permission:evaluate.index');
});