<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;


Route::name('tasks.')->group(function () {
    Route::get('/tasks/need-approval', [TasksController::class, 'needApproval'])->middleware(['permission:task.approve-department', 'permission:task.approve-subordinates'])->name('need-approval');

    Route::get('/tasks', [TasksController::class, 'index'])->middleware('permission:task.index')->name('index');
    Route::get('/tasks/calendar', [TasksController::class, 'calendar'])->middleware('permission:task.index')->name('calendar');
    Route::get('/tasks/create', [TasksController::class, 'create'])->middleware('permission:task.create')->name('create');
    Route::post('/tasks', [TasksController::class,   'store'])->middleware('permission:task.create')->name('store');
    Route::get('/tasks/{task}', [TasksController::class, 'show'])->middleware('permission:task.view')->name('show');
    Route::get('/tasks/{task}/edit', [TasksController::class, 'edit'])->middleware('permission:task.edit')->name('edit');
    Route::post('/tasks/{task}', [TasksController::class, 'update'])->middleware('permission:task.edit')->name('update');
    Route::delete('/tasks/{task}', [TasksController::class, 'destroy'])->middleware('permission:task.delete')->name('destroy');

    Route::get('/tasks/{task}/approve', [TasksController::class, 'approve'])->middleware(['permission:task.approve-department', 'permission:task.approve-subordinates'])->name('approve');
    Route::post('/tasks/{task}/approve', [TasksController::class, 'approveStore'])->middleware(['permission:task.approve-department', 'permission:task.approve-subordinates'])->name('approve.store');
    Route::get('/tasks/{task}/reject', [TasksController::class, 'reject'])->middleware(['permission:task.approve-department', 'permission:task.approve-subordinates'])->name('reject');
    Route::post('/tasks/{task}/reject', [TasksController::class, 'rejectStore'])->middleware(['permission:task.approve-department', 'permission:task.approve-subordinates'])->name('reject.store');

    Route::get('/tasks/{task}/comments', [TasksController::class, 'comments'])->middleware('permission:task.view')->name('comments');
    Route::post('/tasks/{task}/comments', [TasksController::class, 'storeComment'])->middleware('permission:task.create')->name('comments.store');

    Route::get('/tasks/{task}/submit', [TasksController::class, 'submit'])->middleware('permission:task.view')->name('submit');
    Route::post('/tasks/{task}/submit', [TasksController::class, 'submitStore'])->middleware('permission:task.view')->name('submit.store');

    Route::get('/tasks/{task}/attachments/{attachment}', [TasksController::class, 'previewAttachment'])->middleware('permission:task.view')->name('attachments.preview');
    Route::get('/tasks/{task}/proofs/{proof}', [TasksController::class, 'previewProof'])->middleware('permission:task.view')->name('proofs.preview');

    Route::get('/tasks/{task}/attachments/{attachment}/download', [TasksController::class, 'downloadAttachment'])->middleware('permission:task.view')->name('attachments.download');
    Route::get('/tasks/{task}/proofs/{proof}/download', [TasksController::class, 'downloadProof'])->middleware('permission:task.view')->name('proofs.download');

    Route::get('/tasks/executive-plan/create', [TasksController::class, 'executivePlanCreate'])->middleware(['permission:task.assign', 'permission:executive-plan.view-department'])->name('executive-plan-create');
});
