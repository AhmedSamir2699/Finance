<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanSummaryActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'summary_activity_id',
        'completed_at',
        'notes',
    ];

    public function summaryActivity()
    {
        return $this->belongsTo(OperationalPlanSummaryActivity::class);
    }
}
