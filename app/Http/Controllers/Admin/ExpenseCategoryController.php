<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyExpenseCategoryRequest;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        // abort_if(Gate::denies('income_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenseCategories = ExpenseCategory::all();

        return view('admin.expenseCategories.index', compact('expenseCategories'));
    }

    public function create()
    {
        // abort_if(Gate::denies('income_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('expense-categories') => __('breadcrumbs.tasks.index'),
            route('expense-categories.create')
        ];
        return view('admin.expenseCategories.create', compact('breadcrumbs'));
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        $expenseCategory = ExpenseCategory::create($request->all());

        return redirect()->route('expense-categories');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        // abort_if(Gate::denies('income_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('expense-categories') => __('breadcrumbs.tasks.index'),
            route('expense-categories.create')
        ];
        
        return view('admin.expenseCategories.edit', compact('expenseCategory', 'breadcrumbs'));
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        $expenseCategory->update($request->all());

        return redirect()->route('expense-categories');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        // abort_if(Gate::denies('income_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('expenseCategories.show', compact('expenseCategory'));
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        // abort_if(Gate::denies('income_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenseCategory->delete();

        return back();
    }

    // public function massDestroy(MassDestroyExpenseCategoryRequest $request)
    // {
    //     $expenseCategories = ExpenseCategory::find(request('ids'));

    //     foreach ($expenseCategories as $expenseCategory) {
    //         $expenseCategory->delete();
    //     }

    //     return response(null, Response::HTTP_NO_CONTENT);
    // }
}
