<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationalPlanGuestController;

Route::name('operational_plan.')->prefix('/plans')->group(function () {
    Route::get('/', [OperationalPlanGuestController::class, 'index'])->name('index');
    Route::get('/{id}', [OperationalPlanGuestController::class, 'show'])->name('show');
    Route::get('/{id}?department={department}', [OperationalPlanGuestController::class, 'show'])->name('show.department');
    Route::get('/{id}/table-view', [OperationalPlanGuestController::class, 'tableView'])->name('table-view');
    Route::get('/{id}/export', [OperationalPlanGuestController::class, 'export'])->name('export');
});
