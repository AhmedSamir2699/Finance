<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanSummaryActivity extends Model
{
    protected $fillable = [
        'summary_item_id',
        'title',
        'yearly_target',
        'notes',
    ];

    public function summaryItem()
    {
        return $this->belongsTo(OperationalPlanSummaryItem::class);
    }
}
