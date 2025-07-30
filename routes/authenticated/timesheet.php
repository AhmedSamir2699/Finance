<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimesheetController;


Route::name('timesheets.')->group(function () {
    Route::get('/timesheets/{id}/end', [TimesheetController::class, 'endShift'])->middleware('permission:timesheet.edit')->name('endshift');
    Route::get('/timesheets', [TimesheetController::class, 'index'])->middleware('permission:timesheet.index')->name('index');
    
    // Unended timesheets management - must come before parameterized routes
    Route::get('/timesheets/unended', [TimesheetController::class, 'unendedTimesheets'])->middleware('permission:timesheet.edit')->name('unended');
    
    Route::post('/timesheets/export', [TimesheetController::class, 'export'])->middleware('permission:timesheet.export')->name('export');
    Route::get('/timesheets/{date?}', [TimesheetController::class, 'show'])->middleware('permission:timesheet.index')->name('show');
    Route::get('/timesheets/{date}/{timesheet}/edit', [TimesheetController::class, 'editTimesheet'])->middleware('permission:timesheet.edit')->name('edit');
    Route::post('/timesheets/{date}/{timesheet}', [TimesheetController::class, 'updateTimesheet'])->middleware('permission:timesheet.edit')->name('update');

});
