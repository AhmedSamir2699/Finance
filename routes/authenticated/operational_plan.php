<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperationalPlanController;

Route::name('operational-plan.')->group(function () {

    Route::get('/operational-plan/summary/goals', [OperationalPlanController::class, 'summaryGoals'])
        ->name('summary.goals')->middleware('permission:operational_plan.view');

    Route::post('/operational-plan/summary/goals', [OperationalPlanController::class, 'storeSummaryGoals'])
        ->name('summary.goals.store')->middleware('permission:operational_plan.create');








    Route::get('/operational-plan', [OperationalPlanController::class, 'index'])
        ->name('index')->middleware('permission:operational_plan.view');

    Route::post('/operational-plan/{id}/import-excel', [OperationalPlanController::class, 'importExcel'])
        ->name('import-excel')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/create', [OperationalPlanController::class, 'create'])
        ->name('create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan', [OperationalPlanController::class, 'store'])
        ->name('store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}', [OperationalPlanController::class, 'show'])
        ->name('show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/edit', [OperationalPlanController::class, 'edit'])
        ->name('edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}', [OperationalPlanController::class, 'update'])
        ->name('update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/delete', [OperationalPlanController::class, 'destroy'])
        ->name('destroy')->middleware('permission:operational_plan.delete');

    Route::get('/operational-plan/{id}/departments', [OperationalPlanController::class, 'departments'])
        ->name('departments')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/create', [OperationalPlanController::class, 'createDepartment'])
        ->name('departments.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/departments', [OperationalPlanController::class, 'storeDepartment'])
        ->name('departments.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}', [OperationalPlanController::class, 'showDepartment'])
        ->name('departments.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/strategic-goals', [OperationalPlanController::class, 'StrategicGoals'])
        ->name('departments.strategic-goals')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/strategic-goals/create', [OperationalPlanController::class, 'createStrategicGoal'])
        ->name('departments.strategic-goals.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/departments/{departmentId}/strategic-goals', [OperationalPlanController::class, 'storeStrategicGoal'])
        ->name('departments.strategic-goals.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/strategic-goals/{StrategicGoalId}', [OperationalPlanController::class, 'showStrategicGoal'])
        ->name('departments.strategic-goals.show')->middleware('permission:operational_plan.view');

    Route::post('/operational-plan/{id}/departments/{departmentId}/strategic-goals/{StrategicGoalId}', [OperationalPlanController::class, 'updateStrategicGoal'])
        ->name('departments.strategic-goals.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/strategic-goals/{StrategicGoalId}/delete', [OperationalPlanController::class, 'destroyStrategicGoal'])
        ->name('departments.strategic-goals.destroy')->middleware('permission:operational_plan.delete');

    Route::post('/operational-plan/{id}/departments/{departmentId}/strategic-goals/{StrategicGoalId}', [OperationalPlanController::class, 'storeProgram'])
        ->name('departments.programs.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs', [OperationalPlanController::class, 'programs'])
        ->name('departments.programs')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/create', [OperationalPlanController::class, 'createProgram'])
        ->name('departments.programs.create')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}', [OperationalPlanController::class, 'showProgram'])
        ->name('departments.programs.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/edit', [OperationalPlanController::class, 'editProgram'])
        ->name('departments.programs.edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}', [OperationalPlanController::class, 'updateProgram'])
        ->name('departments.programs.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/delete', [OperationalPlanController::class, 'destroyProgram'])
        ->name('departments.programs.destroy')->middleware('permission:operational_plan.delete');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms', [OperationalPlanController::class, 'subPrograms'])
        ->name('departments.subprograms')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/create', [OperationalPlanController::class, 'createSubProgram'])
        ->name('departments.subprograms.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms', [OperationalPlanController::class, 'storeSubProgram'])
        ->name('departments.subprograms.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}', [OperationalPlanController::class, 'showSubProgram'])
        ->name('departments.subprograms.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/edit', [OperationalPlanController::class, 'editSubProgram'])
        ->name('departments.subprograms.edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}', [OperationalPlanController::class, 'updateSubProgram'])
        ->name('departments.subprograms.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/delete', [OperationalPlanController::class, 'destroySubProgram'])
        ->name('departments.subprograms.destroy')->middleware('permission:operational_plan.delete');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items', [OperationalPlanController::class, 'items'])
        ->name('departments.items')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/create', [OperationalPlanController::class, 'createItem'])
        ->name('departments.items.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items', [OperationalPlanController::class, 'storeItem'])
        ->name('departments.items.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}', [OperationalPlanController::class, 'showItem'])
        ->name('departments.items.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/edit', [OperationalPlanController::class, 'editItem'])
        ->name('departments.items.edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}', [OperationalPlanController::class, 'updateItem'])
        ->name('departments.items.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/delete', [OperationalPlanController::class, 'destroyItem'])
        ->name('departments.items.destroy')->middleware('permission:operational_plan.delete');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/activities', [OperationalPlanController::class, 'activities'])
        ->name('departments.activities')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/activities/create', [OperationalPlanController::class, 'createActivity'])
        ->name('departments.activities.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/activities', [OperationalPlanController::class, 'storeActivity'])
        ->name('departments.activities.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/activities/{activityId}', [OperationalPlanController::class, 'showActivity'])
        ->name('departments.activities.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/activities/{activityId}/edit', [OperationalPlanController::class, 'editActivity'])
        ->name('departments.activities.edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/activities/{activityId}', [OperationalPlanController::class, 'updateActivity'])
        ->name('departments.activities.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/departments/{departmentId}/programs/{programId}/subprograms/{subProgramId}/items/{itemId}/activities/{activityId}/delete', [OperationalPlanController::class, 'destroyActivity'])
        ->name('departments.activities.destroy')->middleware('permission:operational_plan.delete');

    Route::get('/operational-plan/{id}/summary', [OperationalPlanController::class, 'summary'])
        ->name('summary')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/summary/create', [OperationalPlanController::class, 'createSummary'])
        ->name('summary.create')->middleware('permission:operational_plan.create');

    Route::post('/operational-plan/{id}/summary', [OperationalPlanController::class, 'storeSummary'])
        ->name('summary.store')->middleware('permission:operational_plan.create');

    Route::get('/operational-plan/{id}/summary/{summaryId}', [OperationalPlanController::class, 'showSummary'])
        ->name('summary.show')->middleware('permission:operational_plan.view');

    Route::get('/operational-plan/{id}/summary/{summaryId}/edit', [OperationalPlanController::class, 'editSummary'])
        ->name('summary.edit')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/summary/{summaryId}', [OperationalPlanController::class, 'updateSummary'])
        ->name('summary.update')->middleware('permission:operational_plan.edit');

    Route::post('/operational-plan/{id}/summary/{summaryId}/delete', [OperationalPlanController::class, 'destroySummary'])
        ->name('summary.destroy')->middleware('permission:operational_plan.delete');
});
