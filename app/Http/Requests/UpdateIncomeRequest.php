<?php

namespace App\Http\Requests;

use App\Models\Income;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateIncomeRequest extends FormRequest
{
    // public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'income_category_id'           => ['required','exists:income_categories,id'],
            'entry_date'                   => ['required','date'],
            'amount'                       => ['required','numeric','min:0.01'],
            'description'                  => ['nullable','string','max:255'],

            'allocations'                  => ['required','array','min:1'],
            'allocations.*.finance_item_id'=> ['required','integer','exists:finance_items,id'],
            'allocations.*.percentage'     => ['required','numeric','min:0.001'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $alloc = collect($this->input('allocations', []));

            if ($alloc->pluck('finance_item_id')->duplicates()->isNotEmpty()) {
                $v->errors()->add('allocations', __('Duplicate finance items are not allowed.'));
            }

            $sum = (float) $alloc->sum('percentage');
            if ($sum > 100.0 + 0.01) {
                $v->errors()->add('allocations', __('Total percentages must be ≤ 100%. Current total: :sum%', ['sum' => $sum]));
            }

            $nonLeaf = \App\Models\FinanceItem::whereIn('id', $alloc->pluck('finance_item_id'))
                ->whereHas('children')->exists();
            if ($nonLeaf) {
                $v->errors()->add('allocations', __('Allocations must target leaf finance items only.'));
            }
        });
    }
}
