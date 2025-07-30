<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ExecutivePlanController;

Route::name('users.')->group(function () {

    Route::get('/users/reports', [UsersController::class, 'reportsIndex'])->name('reports.index')->middleware('permission:report.index');
    Route::get('/users/{user}', [UsersController::class, 'show'])->middleware('permission:user.view')->name('show');

    Route::get('/users/{user}/timesheet', [UsersController::class, 'timesheet'])->middleware('permission:user.view')->name('timesheet');
    Route::name('reports.')->group(function () {
        Route::get('/users/{user}/reports', [UsersController::class, 'customDateReport'])->name('show')->middleware('permission:report.view');
        Route::get('/users/{user}/reports/export', [UsersController::class, 'exportCustomDateReport'])->name('export')->middleware('permission:report.view');
    });

    Route::name('summary.')->group(function () {
        Route::get('/users/{user}/summary', [UsersController::class, 'userSummaryReport'])->name('show')->middleware('permission:report.summary');
        Route::get('/users/{user}/summary/pdf', [UsersController::class, 'exportSummaryToPdf'])->name('pdf')->middleware('permission:report.summary');
        Route::get('/profile/summary', [UsersController::class, 'mySummaryReport'])->name('my')->middleware('auth');
        Route::get('/profile/summary/pdf', [UsersController::class, 'mySummaryReportPdf'])->name('my.pdf')->middleware('auth');
    });

    Route::name('summary.general.')->group(function () {
        Route::get('/users/summary/general', [UsersController::class, 'generalSummaryIndex'])->name('index')->middleware('permission:report.summary');
        Route::get('/users/summary/general/department/{department}', [UsersController::class, 'generalSummaryUsers'])->name('department')->middleware('permission:report.summary');
    });

    Route::name('executive-plan.')->prefix('/users')->group(function ($user) {
        Route::get('/{user}/executive-plan', [ExecutivePlanController::class, 'index'])->name('index');
        Route::get('/{user}/executive-plan/migrate', [ExecutivePlanController::class, 'migrate'])->name('migrate');
        Route::post('/{user}/executive-plan/migrate', [ExecutivePlanController::class, 'migrateStore'])->name('migrate.store');
        Route::get('/executive-plan/show/{month?}/{year?}', [ExecutivePlanController::class, 'show'])->name('show');
        Route::get('/executive-plan/export/{year?}/{user?}', [ExecutivePlanController::class, 'exportToExcel'])->name('export');
    });
});
