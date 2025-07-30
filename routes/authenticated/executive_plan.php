<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExecutivePlanController;

Route::name('executive-plan.')->group(function () {
    Route::get('/executive-plan', [ExecutivePlanController::class, 'index'])->name('index');
    Route::get('/executive-plan/cell/{cell}', [ExecutivePlanController::class, 'showCell'])->name('cell.show');
    Route::get('/executive-plan/cell/{cell}/edit', [ExecutivePlanController::class, 'editCell'])->name('cell.edit');
    Route::post('/executive-plan/cell/{cell}', [ExecutivePlanController::class, 'editCellStore'])->name('cell.update');
    Route::get('/executive-plan/show/{month?}/{year?}', [ExecutivePlanController::class, 'show'])->name('show');
    Route::get('/executive-plan/export/{year?}/{user?}', [ExecutivePlanController::class, 'exportToExcel'])->name('export');
    Route::get('/executive-plan/clone/{month?}/{year?}', [ExecutivePlanController::class, 'clone'])->name('clone');
    Route::post('/executive-plan/clone/{month?}/{year?}', [ExecutivePlanController::class, 'cloneStore'])->name('clone.store');

});