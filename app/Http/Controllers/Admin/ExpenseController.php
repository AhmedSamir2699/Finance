<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyExpenseRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinanceItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends Controller
{
    public function index()
    {
        // abort_if(Gate::denies('expense_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expenses = Expense::with(['expense_category', 'finance_item'])->get();
        $totalexpenses = Expense::totalLeafAmount();
        return view('admin.expenses.index', compact('expenses','totalexpenses'));
    }

    public function create()
    {
        // abort_if(Gate::denies('expense_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('expenses') => __('breadcrumbs.expenses'),
            route('expenses.create')
        ];

        $expense_categories = ExpenseCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finance_items = $this->buildFinanceItemOptions();
        return view('admin.expenses.create', compact('breadcrumbs','expense_categories', 'finance_items'));
    }

    public function buildFinanceItemOptions()
    {
        $items = FinanceItem::all(); // simple query
        $options = [];

        foreach ($items as $item) {
            $fullName = $this->buildFullName($item, $items);
            $options[$item->id] = $fullName;
        }

        return $options;
    }

    private function buildFullName($item, $allItems, $separator = ' > ')
    {
        $names = [$item->name];
        while ($item->parent_id) {
            $parent = $allItems->firstWhere('id', $item->parent_id);
            if (!$parent)
                break;
            array_unshift($names, $parent->name);
            $item = $parent;
        }
        return implode($separator, $names);
    }



    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create($request->all());

        return redirect()->route('expenses');
    }

    public function edit(Expense $expense)
    {
        // abort_if(Gate::denies('expense_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense_categories = ExpenseCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

       $finance_items = $this->buildFinanceItemOptions();

        $expense->load('expense_category', 'finance_item');

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('expenses') => __('breadcrumbs.expenses'),
            route('expenses.edit',$expense)
        ];

        return view('admin.expenses.edit', compact('expense', 'expense_categories', 'finance_items','breadcrumbs'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->all());

        return redirect()->route('expenses');
    }

    public function show(Expense $expense)
    {
        abort_if(Gate::denies('expense_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->load('expense_category', 'finance_item');

        return view('admin.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        // abort_if(Gate::denies('expense_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $expense->delete();

        return back();
    }

    public function massDestroy(MassDestroyExpenseRequest $request)
    {
        $expenses = Expense::find(request('ids'));

        foreach ($expenses as $expense) {
            $expense->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
