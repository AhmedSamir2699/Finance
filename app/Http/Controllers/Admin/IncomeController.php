<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyIncomeRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\FinanceItem;
use App\Models\Income;
use App\Models\IncomeCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends Controller
{
    public function index()
    {
        // abort_if(Gate::denies('income_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incomes = Income::with(['income_category', 'finance_item'])->get();
        $totalincomes = Income::totalLeafAmount();
        return view('admin.incomes.index', compact('incomes', 'totalincomes'));
    }

    public function create()
    {
        // abort_if(Gate::denies('income_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('incomes') => __('breadcrumbs.incomes'),
            route('incomes.create')
        ];
        $income_categories = IncomeCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finance_items = $this->buildFinanceItemOptions();
        return view('admin.incomes.create', compact('finance_items', 'income_categories','breadcrumbs'));
    }

    public function buildFinanceItemOptions()
{
    // Get only leaf items (items with no children)
    $leafItems = FinanceItem::doesntHave('children')->get();

    // Also fetch all items to build the full names
    $allItems = FinanceItem::all();

    $options = [];

    foreach ($leafItems as $item) {
        $fullName = $this->buildFullName($item, $allItems);
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

    public function store(StoreIncomeRequest $request)
    {
        $income = Income::create($request->all());

        return redirect()->route('incomes');
    }

    public function edit(Income $income)
    {
        // abort_if(Gate::denies('income_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $income_categories = IncomeCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $finance_items = $this->buildFinanceItemOptions();

        $income->load('income_category', 'finance_item');
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('incomes') => __('breadcrumbs.incomes'),
            route('incomes.edit',$income)
        ];
        return view('admin.incomes.edit', compact('finance_items', 'income', 'income_categories','breadcrumbs'));
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
        $income->update($request->all());

        return redirect()->route('incomes');
    }

    public function show(Income $income)
    {
        abort_if(Gate::denies('income_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $income->load('income_category', 'finance_item');

        return view('admin.incomes.show', compact('income'));
    }

    public function destroy(Income $income)
    {
        abort_if(Gate::denies('income_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $income->delete();

        return back();
    }

    public function massDestroy(MassDestroyIncomeRequest $request)
    {
        $incomes = Income::find(request('ids'));

        foreach ($incomes as $income) {
            $income->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
