<?php

namespace App\Http\Requests;

use App\Models\FinanceItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFinanceItemRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return Gate::allows('finance_item_create');
    // }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'amount' => [
                'nullable',
            ],
        ];
    }
}
