<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\FinanceItem;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        // Let Livewire render the list; no need to prefetch items here.
        return view('admin.budgets.index');
    }

    public function create()
    {
        // Select list of Finance Items (budgets reference finance_items)
        $financeItems = FinanceItem::orderBy('name')->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('budgets')   => __('breadcrumbs.budgets'),
            route('budgets.create'),
        ];

        return view('admin.budgets.create', compact('financeItems', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'finance_item_id' => ['required', 'exists:finance_items,id'],
            'goal'            => ['nullable', 'string', 'max:255'],
        ]);

        Budget::create($data);

        return redirect()->route('budgets')->with('status', __('global.saved'));
    }

    public function edit(Budget $budget)
    {
        $financeItems = FinanceItem::orderBy('name')->pluck('name', 'id')
            ->prepend(trans('global.pleaseSelect'), '');

        $budget->load('financeItem');

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('budgets')   => __('breadcrumbs.budgets'),
            route('budgets.edit', $budget),
        ];

        return view('admin.budgets.edit', compact('budget', 'financeItems', 'breadcrumbs'));
    }

    public function update(Request $request, Budget $budget)
    {
        $data = $request->validate([
            'finance_item_id' => ['required', 'exists:finance_items,id'],
            'goal'            => ['nullable', 'string', 'max:255'],
        ]);

        $budget->update($data);

        return redirect()->route('budgets')->with('status', __('global.saved'));
    }

    public function show(Budget $budget)
    {
        $budget->load('financeItem');

        return view('admin.budgets.show', compact('budget'));
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->route('budgets')->with('status', __('global.deleted'));
    }
}
