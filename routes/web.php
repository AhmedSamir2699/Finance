<?php

use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\ElectionsController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Facades\Auth;




Route::name('guest.')->group(function () {
    require __DIR__ . ('/guest/plan.php');
});


require __DIR__ . '/guest/election.php';

// Static Pages Public Routes
Route::get('/pages', [\App\Http\Controllers\StaticPagesController::class, 'index'])->name('static-pages.index');
Route::get('/pages/{slug}', [\App\Http\Controllers\StaticPagesController::class, 'show'])->name('static-pages.show');

// Image Upload Route
Route::post('/upload-image', [\App\Http\Controllers\ImageUploadController::class, 'upload'])->name('upload-image')->middleware('auth');


Route::get('/error/{code}', function ($code) {
    abort($code);
})->name('error');




Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/my-department', [DepartmentsController::class, 'myDepartment'])->name('my-department');


    require __DIR__ . '/authenticated/profile.php';

    require __DIR__ . '/authenticated/notifications.php';

    require __DIR__ . '/authenticated/executive_plan.php';

    require __DIR__ . '/authenticated/evaluate.php';
    
    require __DIR__ . '/authenticated/operational_plan.php';

    require __DIR__ . '/authenticated/timesheet.php';

    require __DIR__ . '/authenticated/messages.php';

    require __DIR__ . '/authenticated/users.php';

    require __DIR__ . '/authenticated/tasks.php';

    require __DIR__ . '/authenticated/manage.php';


    Route::name('requests.')->group(function () {
        Route::get('/requests', [RequestsController::class, 'index'])->middleware('permission:request.index')->name('index');
        Route::get('/requests/create', [RequestsController::class, 'create'])->middleware('permission:request.create')->name('create');
        Route::post('/requests', [RequestsController::class, 'store'])->middleware('permission:request.create')->name('store');
        Route::get('/requests/{request}', [RequestsController::class, 'show'])->middleware('permission:request.view')->name('show');
        Route::get('/requests/{request}/return', [RequestsController::class, 'return'])->middleware('permission:request.edit')->name('return');
        Route::post('/requests/{request}/return', [RequestsController::class, 'returnStore'])->middleware('permission:request.edit')->name('return.store');
        Route::get('/requests/{request}/approve', [RequestsController::class, 'approve'])->middleware('permission:request.approve')->name('approve');
        Route::post('/requests/{request}/approve', [RequestsController::class, 'approveStore'])->middleware('permission:request.approve')->name('approve.store');
        Route::get('/requests/{request}/reject', [RequestsController::class, 'reject'])->middleware('permission:request.edit')->name('reject');
        Route::post('/requests/{request}/reject', [RequestsController::class, 'rejectStore'])->middleware('permission:request.edit')->name('reject.store');
    });









    // Impersonate routes
    Route::get('/impersonate/{user}', function (User $user) {
        Auth::user()->impersonate($user);
        return redirect()->route('dashboard');
    })->name('users.impersonate')->middleware('permission:user.impersonate');

    Route::get('/stop-impersonate', function () {
        Auth::user()->stopImpersonating();
        return redirect()->route('dashboard');
    })->name('users.stop-impersonate');
});

if (env('APP_ENV') == 'production') {
    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/employee/livewire/update', $handle);
    });
}

require __DIR__ . '/auth.php';
