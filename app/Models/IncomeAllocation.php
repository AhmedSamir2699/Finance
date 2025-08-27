<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeAllocation extends Model
{
    protected $fillable = ['income_id', 'finance_item_id', 'percentage', 'amount'];

    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    public function financeItem()
    {
        return $this->belongsTo(FinanceItem::class);
    }
}
