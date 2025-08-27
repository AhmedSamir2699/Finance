<?php

namespace App\Http\Requests;

use App\Models\Income;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreIncomeRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return Gate::allows('income_create');
    // }

    public function rules()
    {
        return [
            'entry_date' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'description' => [
                'string',
                'nullable',
            ],

            // allocations: [{finance_item_id, percentage}]
            'allocations' => ['required', 'array', 'min:1'],
            'allocations.*.finance_item_id' => ['required', 'integer', 'exists:finance_items,id'],
            'allocations.*.percentage' => ['required', 'numeric', 'min:0.001'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $alloc = collect($this->input('allocations', []));

            // duplicates?
            if ($alloc->pluck('finance_item_id')->duplicates()->isNotEmpty()) {
                $v->errors()->add('allocations', __('Duplicate finance items are not allowed.'));
            }

            // sum <= 100 (tolerance 0.01)
            $sum = (float) $alloc->sum('percentage');
            if ($sum < 0.0 || $sum > 100.0 + 0.01) {
                $v->errors()->add('allocations', __('Total percentages must be ≤ 100%. Current total: :sum%', ['sum' => $sum]));
            }

            // (optional) leaf-only check
            $nonLeaf = \App\Models\FinanceItem::whereIn('id', $alloc->pluck('finance_item_id'))
                ->whereHas('children')->exists();
            if ($nonLeaf) {
                $v->errors()->add('allocations', __('Allocations must target leaf finance items only.'));
            }
        });
    }

}
