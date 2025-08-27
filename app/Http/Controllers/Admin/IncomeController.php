<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyIncomeRequest;
use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\FinanceItem;
use App\Models\Income;
use App\Models\IncomeAllocation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
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
        return view('admin.incomes.create', compact('finance_items', 'income_categories', 'breadcrumbs'));
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
        return DB::transaction(function () use ($request) {
            // create income first
            $income = Income::create($request->only([
                'income_category_id',
                'entry_date',
                'amount',
                'description'
            ]));

            // applyPercentAndUpdateItems allocations
            $this->applyPercentAndUpdateItems($income, $request->input('allocations', []));

            return redirect()->route('incomes')->with('success', __('Income saved and allocated.'));
        });
    }

    public function applyPercentAndUpdateItems(Income $income, array $allocations): void
    {
        DB::transaction(function () use ($income, $allocations) {
            $totalAmount = (float) $income->amount;
            if ($totalAmount <= 0)
                throw new InvalidArgumentException('Income amount must be > 0.');

            // Revert previous allocations from item totals
            $previous = $income->allocations()->get();
            foreach ($previous as $row) {
                FinanceItem::whereKey($row->finance_item_id)
                    ->update(['amount' => DB::raw('amount - ' . (float) $row->amount)]);
            }
            $income->allocations()->delete();

            // Compute + persist
            $sumPct = 0.0;
            foreach ($allocations as $a) {
                $sumPct += (float) ($a['percentage'] ?? 0);
            }
            if ($sumPct > 100.0 + 0.01) {
                throw new InvalidArgumentException('Total percentages cannot exceed 100%.');
            }

            foreach ($allocations as $a) {
                $fi = (int) $a['finance_item_id'];
                $pct = (float) $a['percentage'];
                $amt = round($totalAmount * $pct / 100, 2, PHP_ROUND_HALF_EVEN);

                IncomeAllocation::create([
                    'income_id' => $income->id,
                    'finance_item_id' => $fi,
                    'percentage' => $pct,
                    'amount' => $amt,
                ]);

                FinanceItem::whereKey($fi)
                    ->update(['amount' => DB::raw('amount + ' . (float) $amt)]);
            }
        });
    }

    public function revertAndDelete(Income $income): void
    {
        DB::transaction(function () use ($income) {
            $allocs = $income->allocations()->get();
            foreach ($allocs as $row) {
                FinanceItem::whereKey($row->finance_item_id)
                    ->update(['amount' => DB::raw('amount - ' . (float) $row->amount)]);
            }
            $income->allocations()->delete();
        });
    }

    public function edit(Income $income)
    {
        // $income already loaded
        $income->load(['income_category', 'finance_item', 'allocations.financeItem']);

        $income_categories = IncomeCategory::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $finance_items = $this->buildFinanceItemOptions();

        // Build prefill for the repeater (percent-based)
        $prefill = $income->allocations->map(fn($a) => [
            'finance_item_id' => $a->finance_item_id,
            'percentage' => (float) $a->percentage, // we stored it before
            'amount' => (float) $a->amount,     // helpful for preview
        ])->values();

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('incomes') => __('breadcrumbs.incomes'),
            route('incomes.edit', $income),
        ];

        return view('admin.incomes.edit', compact(
            'income',
            'income_categories',
            'finance_items',
            'breadcrumbs'
        ))->with('allocationsPrefill', $prefill);
    }

    public function update(UpdateIncomeRequest $request, Income $income)
    {
       
        return DB::transaction(function () use ($request, $income) {
            $income->update($request->only([
                'income_category_id',
                'entry_date',
                'amount',
                'description'
            ]));

            $this->applyPercentAndUpdateItems($income, $request->input('allocations', []));

            return redirect()->route('incomes')->with('success', __('Income updated and allocations applied.'));
        });
    }



    public function destroy(Income $income)
    {
        // abort_if(Gate::denies('income_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->revertAndDelete($income); // subtract from items and delete allocations
        $income->delete();

        return back()->with('success', __('Income deleted and item totals reverted.'));
    }

    public function show(Income $income)
    {
        abort_if(Gate::denies('income_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $income->load('income_category', 'finance_item');

        return view('admin.incomes.show', compact('income'));
    }

    // public function destroy(Income $income)
    // {
    //     abort_if(Gate::denies('income_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    //     $income->delete();

    //     return back();
    // }

    public function massDestroy(MassDestroyIncomeRequest $request)
    {
        $incomes = Income::find(request('ids'));

        foreach ($incomes as $income) {
            $income->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
