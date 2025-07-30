<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\ElectionsController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\SidebarLinkController;
use App\Http\Controllers\Manage\AttendanceSettingsController;
use App\Http\Controllers\Manage\SettingsController;
use App\Http\Controllers\Manage\EvaluationCriteriaController;
use App\Http\Controllers\Manage\StaticPagesController;
use App\Http\Controllers\Manage\MaintenanceController;

Route::name('manage.')->prefix('/manage')->group(function () {

    Route::get('/', [SettingsController::class, 'index'])->name('index')->middleware('permission:manage.settings');
    
    // Settings Management
    Route::name('settings.')->middleware('permission:manage.settings')->group(function () {
        Route::get('/settings', [SettingsController::class, 'edit'])->name('edit');
    });

    Route::name('elections.')->prefix('/elections')->group(function () {
        Route::get('/', [ElectionsController::class, 'manageIndex'])->middleware('permission:elections.index')->name('index');
        Route::get('/create', [ElectionsController::class, 'manageCreate'])->middleware('permission:elections.index')->name('create');
        Route::post('/', [ElectionsController::class, 'manageStore'])->middleware('permission:elections.index')->name('store');
        Route::get('/{election}', [ElectionsController::class, 'manageShow'])->middleware('permission:elections.index')->name('show');
        Route::get('/{election}/edit', [ElectionsController::class, 'manageEdit'])->middleware('permission:elections.index')->name('edit');
        Route::get('/{election}/votes', [ElectionsController::class, 'manageVotes'])->middleware('permission:elections.index')->name('votes');
        Route::post('/{election}', [ElectionsController::class, 'manageUpdate'])->middleware('permission:elections.index')->name('update');
        Route::post('/{election}/delete', [ElectionsController::class, 'manageDelete'])->middleware('permission:elections.index')->name('destroy');
        Route::post('/{election}/clear-votes', [ElectionsController::class, 'manageClearVotes'])->middleware('permission:elections.index')->name('clearVotes');
    });

    Route::name('forms.')->prefix('/forms')->group(function () {
        Route::get('/', [FormsController::class, 'index'])->middleware('permission:role.index')->name('index');
        Route::get('/create', [FormsController::class, 'create'])->middleware('permission:role.index')->name('create');
        Route::post('/', [FormsController::class, 'store'])->middleware('permission:role.index')->name('store');
        Route::get('/{form}', [FormsController::class, 'show'])->middleware('permission:role.index')->name('show');
        Route::get('/{form}/fields', [FormsController::class, 'fields'])->middleware('permission:role.index')->name('fields');
        Route::get('/{form}/edit', [FormsController::class, 'edit'])->middleware('permission:role.index')->name('edit');
        Route::post('/{form}', [FormsController::class, 'update'])->middleware('permission:role.index')->name('update');
        Route::post('/{form}/delete', [FormsController::class, 'destroy'])->middleware('permission:role.index')->name('destroy');
        Route::get('/{form}/fields-position', [FormsController::class, 'fieldPosition'])->middleware('permission:role.index')->name('field-position');
    });

    Route::name('users.')->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->middleware('permission:user.index')->name('index');
        Route::get('/users/create', [UsersController::class, 'create'])->middleware('permission:user.create')->name('create');
        Route::post('/users', [UsersController::class, 'store'])->middleware('permission:user.create')->name('store');
        Route::get('/users/{user}', [UsersController::class, 'show'])->middleware('permission:user.view')->name('show');
        Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->middleware('permission:user.edit')->name('edit');
        Route::post('/users/{user}', [UsersController::class, 'update'])->middleware('permission:user.edit')->name('update');
        Route::delete('/users/{user}', [UsersController::class, 'destroy'])->middleware('permission:user.delete')->name('destroy');
    });

    Route::name('departments.')->group(function () {
        Route::get('/departments', [DepartmentsController::class, 'index'])->middleware('permission:department.index')->name('index');
        Route::get('/departments/create', [DepartmentsController::class, 'create'])->middleware('permission:department.create')->name('create');
        Route::get('/departments/{department}', [DepartmentsController::class, 'show'])->middleware('permission:department.view-any')->name('show');
        Route::get('/departments/{department}/edit', [DepartmentsController::class, 'edit'])->middleware('permission:department.edit')->name('edit');
        Route::post('/departments', [DepartmentsController::class, 'store'])->middleware('permission:department.create')->name('store');
        Route::post('/departments/{department}', [DepartmentsController::class, 'update'])->middleware('permission:department.edit')->name('update');
        Route::get('/departments/{department}/delete', [DepartmentsController::class, 'destroy'])->middleware('permission:department.delete')->name('destroy');
    });

    Route::name('roles.')->group(function () {
        Route::get('/roles', [RolesController::class, 'index'])->middleware('permission:role.index')->name('index');
        Route::get('/roles/create', [RolesController::class, 'create'])->middleware('permission:role.create')->name('create');
        Route::post('/roles', [RolesController::class, 'store'])->middleware('permission:role.create')->name('store');
        Route::get('/roles/{role}', [RolesController::class, 'show'])->middleware('permission:role.view')->name('show');
        Route::get('/roles/{role}/edit', [RolesController::class, 'edit'])->middleware('permission:role.edit')->name('edit');
        Route::get('/roles/{role}/permissions', [RolesController::class, 'permissions'])->middleware('permission:role.edit')->name('edit.permissions');
        Route::post('/roles/{role}/permissions', [RolesController::class, 'permissionsStore'])->middleware('permission:role.edit')->name('store.permissions');
        Route::post('/roles/{role}', [RolesController::class, 'update'])->middleware('permission:role.edit')->name('update');
        Route::delete('/roles/{role}', [RolesController::class, 'destroy'])->middleware('permission:role.delete')->name('destroy');
    });

    Route::get('/roles', [RolesController::class, 'index'])->middleware('permission:role.index')->name('roles.index');
    Route::get('/permissions', [RolesController::class, 'permissions'])->middleware('permission:role.index')->name('permissions.index');
    Route::get('/logs', [RolesController::class, 'logs'])->middleware('permission:role.index')->name('logs.index');

    // Sidebar Links Management
    Route::name('sidebar-links.')->middleware('permission:manage.settings')->group(function () {
        Route::get('/sidebar-links', [SidebarLinkController::class, 'index'])->name('index');
        Route::get('/sidebar-links/create', [SidebarLinkController::class, 'create'])->name('create');
        Route::post('/sidebar-links', [SidebarLinkController::class, 'store'])->name('store');
        Route::get('/sidebar-links/{sidebarLink}/edit', [SidebarLinkController::class, 'edit'])->name('edit');
        Route::put('/sidebar-links/{sidebarLink}', [SidebarLinkController::class, 'update'])->name('update');
        Route::delete('/sidebar-links/{sidebarLink}', [SidebarLinkController::class, 'destroy'])->name('destroy');
        Route::post('/sidebar-links/reorder', [SidebarLinkController::class, 'reorder'])->name('reorder');
        Route::patch('/sidebar-links/{sidebarLink}/toggle-status', [SidebarLinkController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Evaluation Criteria Management
    Route::name('evaluation-criteria.')->middleware('permission:manage.settings')->group(function () {
        Route::get('/evaluation-criteria', [EvaluationCriteriaController::class, 'index'])->name('index');
        Route::get('/evaluation-criteria/create', [EvaluationCriteriaController::class, 'create'])->name('create');
        Route::post('/evaluation-criteria', [EvaluationCriteriaController::class, 'store'])->name('store');
        Route::get('/evaluation-criteria/{criteria}/edit', [EvaluationCriteriaController::class, 'edit'])->name('edit');
        Route::put('/evaluation-criteria/{criteria}', [EvaluationCriteriaController::class, 'update'])->name('update');
        Route::delete('/evaluation-criteria/{criteria}', [EvaluationCriteriaController::class, 'destroy'])->name('destroy');
    });

    // Static Pages Management
    Route::name('static-pages.')->middleware('permission:manage.settings')->group(function () {
        Route::get('/static-pages', [StaticPagesController::class, 'index'])->name('index');
        Route::get('/static-pages/create', [StaticPagesController::class, 'create'])->name('create');
        Route::post('/static-pages', [StaticPagesController::class, 'store'])->name('store');
        Route::get('/static-pages/{staticPage}/edit', [StaticPagesController::class, 'edit'])->name('edit');
        Route::put('/static-pages/{staticPage}', [StaticPagesController::class, 'update'])->name('update');
        Route::delete('/static-pages/{staticPage}', [StaticPagesController::class, 'destroy'])->name('destroy');
        Route::patch('/static-pages/{staticPage}/toggle-status', [StaticPagesController::class, 'toggleStatus'])->name('toggle-status');
    });
});

// Attendance Settings
Route::middleware(['auth', 'can:manage.settings'])->prefix('manage')->group(function () {
    Route::get('attendance-settings', [AttendanceSettingsController::class, 'edit'])->name('manage.attendance-settings.edit');
    Route::post('attendance-settings', [AttendanceSettingsController::class, 'update'])->name('manage.attendance-settings.update');
});

// System Maintenance
Route::middleware(['auth', 'can:manage.settings'])->prefix('manage')->group(function () {
    Route::get('maintenance', [MaintenanceController::class, 'index'])->name('manage.maintenance.index');
    Route::post('maintenance/clear-cache', [MaintenanceController::class, 'clearCache'])->name('manage.maintenance.clear-cache');
    Route::post('maintenance/clear-log', [MaintenanceController::class, 'clearLog'])->name('manage.maintenance.clear-log');
    Route::post('maintenance/reset-settings', [MaintenanceController::class, 'resetSettings'])->name('manage.maintenance.reset-settings');
    Route::get('maintenance/get-log', [MaintenanceController::class, 'getLogContent'])->name('manage.maintenance.get-log');
    Route::get('maintenance/debug-env', [MaintenanceController::class, 'debugEnvironment'])->name('manage.maintenance.debug-env');
}); 