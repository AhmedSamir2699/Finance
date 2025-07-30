<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanSummaryItem extends Model
{
    protected $fillable = [
        'summary_program_id',
        'title',
        'quantity',
        'detailed_expected_cost',
        'total_expected_cost',
        'detailed_expected_revenue',
        'total_expected_revenue',
    ];

    public function summaryProgram()
    {
        return $this->belongsTo(OperationalPlanSummaryProgram::class);
    }

    public function activities()
    {
        return $this->hasMany(OperationalPlanSummaryActivity::class, 'summary_item_id');
    }
}
