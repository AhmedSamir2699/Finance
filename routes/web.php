<?php

use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\FinanceItemsController;
use App\Http\Controllers\Admin\IncomeCategoryController;
use App\Http\Controllers\Admin\IncomeController;
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

    // Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    // Permissions

    // Expense Category
    Route::get('expense-categories', [ExpenseCategoryController::class, 'index'])->name('expense-categories');
    Route::get('expense-categories/create', [ExpenseCategoryController::class, 'create'])->name('expense-categories.create');
    Route::post('expense-categories/store', [ExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('expense-categories/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])
        ->name('expense-categories.edit');

    Route::post('expense-categories/{expenseCategory}/update', [ExpenseCategoryController::class, 'update'])
        ->name('expense-categories.update');

    Route::get('expense-categories/show', [ExpenseCategoryController::class, 'show'])->name('expense-categories.show');
    Route::delete('expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])
        ->name('expense-categories.destroy');

    // Income Category
    Route::get('income-categories', [IncomeCategoryController::class, 'index'])->name('income-categories');
    Route::get('income-categories/create', [IncomeCategoryController::class, 'create'])->name('income-categories.create');
    Route::post('income-categories/store', [IncomeCategoryController::class, 'store'])->name('income-categories.store');
    Route::get('income-categories/{incomeCategory}/edit', [IncomeCategoryController::class, 'edit'])
        ->name('income-categories.edit');

    Route::post('income-categories/{incomeCategory}/update', [IncomeCategoryController::class, 'update'])
        ->name('income-categories.update');

    Route::get('income-categories/show', [IncomeCategoryController::class, 'show'])->name('income-categories.show');
    Route::delete('income-categories/{incomeCategory}', [IncomeCategoryController::class, 'destroy'])
        ->name('income-categories.destroy');

    // // Expense
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses/store', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])
        ->name('expenses.edit');

    Route::post('expenses/{expense}/update', [ExpenseController::class, 'update'])
        ->name('expenses.update');

    Route::get('expenses/show', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])
        ->name('expenses.destroy');

    // Incomes
    Route::get('incomes', [IncomeController::class, 'index'])->name('incomes');
    Route::get('incomes/create', [IncomeController::class, 'create'])->name('incomes.create');
    Route::post('incomes/store', [IncomeController::class, 'store'])->name('incomes.store');
    Route::get('incomes/{income}/edit', [IncomeController::class, 'edit'])
        ->name('incomes.edit');

    Route::post('incomes/{income}/update', [IncomeController::class, 'update'])
        ->name('incomes.update');

    Route::get('incomes/show', [IncomeController::class, 'show'])->name('incomes.show');
    Route::delete('incomes/{income}', [IncomeController::class, 'destroy'])
        ->name('incomes.destroy');

    // // Expense Report
    // Route::delete('expense-reports/destroy', 'ExpenseReportController@massDestroy')->name('expense-reports.massDestroy');
    // Route::resource('expense-reports', 'ExpenseReportController');

    // Finance Items
// ✅ Correct Route
Route::get('finance-items/destroy/{financeItem}', [FinanceItemsController::class, 'destroy'])
    ->name('finance-items.destroy');
    // Route::post('finance-items/parse-csv-import', 'FinanceItemsController@parseCsvImport')->name('finance-items.parseCsvImport');
    // Route::post('finance-items/process-csv-import', 'FinanceItemsController@processCsvImport')->name('finance-items.processCsvImport');
    // Route::resource('finance-items', 'FinanceItemsController');
    Route::get('finance-items', [FinanceItemsController::class, 'index'])->name('finance-items');
    Route::post('finance-items/store', [FinanceItemsController::class, 'store'])->name('finance-items.store');
    Route::post('finance-items/reorder', [FinanceItemsController::class, 'reorder'])->name('finance-items.reorder');
    // });









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
