<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFinanceItemRequest;
use App\Http\Requests\StoreFinanceItemRequest;
use App\Http\Requests\UpdateFinanceItemRequest;
use App\Models\FinanceItem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FinanceItemsController extends Controller
{
   

  

    public function index()
{
    // اجلب فقط البنود الجذرية ومع أطفالها المتداخلة
    $items = FinanceItem::with('children')->whereNull('parent_id')->get();

    // كل البنود لأي استعمال في الـ select (للبند الأب)
    $allItems = FinanceItem::all();

    $totalBudget = FinanceItem::totalLeafAmount();

    return view('admin.financeItems.index', compact('items', 'allItems', 'totalBudget'));
}


    public function create()
    {
        abort_if(Gate::denies('finance_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = FinanceItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.financeItems.create', compact('parents'));
    }

    public function store(StoreFinanceItemRequest $request)
    {
        $financeItem = FinanceItem::create($request->all());

        return redirect()->route('finance-items');
    }

    public function edit(FinanceItem $financeItem)
    {
        abort_if(Gate::denies('finance_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = FinanceItem::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $financeItem->load('parent');

        return view('admin.financeItems.edit', compact('financeItem', 'parents'));
    }

    public function update(UpdateFinanceItemRequest $request, FinanceItem $financeItem)
    {
        $financeItem->update($request->all());

        return redirect()->route('finance-items');
    }

    public function show(FinanceItem $financeItem)
    {
        abort_if(Gate::denies('finance_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $financeItem->load('parent');

        return view('admin.financeItems.show', compact('financeItem'));
    }

    public function destroy(FinanceItem $financeItem)
    {
        // abort_if(Gate::denies('finance_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $financeItem->delete();

        return redirect()->route('finance-items');
    }

    public function massDestroy(MassDestroyFinanceItemRequest $request)
    {
        $financeItems = FinanceItem::find(request('ids'));

        foreach ($financeItems as $financeItem) {
            $financeItem->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function reorder(Request $request)
{
    $this->updateOrder($request->input('data'));
    return response()->json(['status' => 'ok']);
}

private function updateOrder($items, $parentId = null)
{
    foreach ($items as $item) {
        FinanceItem::where('id', $item['id'])->update(['parent_id' => $parentId]);
        if (isset($item['children'])) {
            $this->updateOrder($item['children'], $item['id']);
        }
    }
}

}
