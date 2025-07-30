<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanSummaryProgram extends Model
{
    protected $fillable = [
        'operational_plan_id',
        'strategic_goal_id',
        'title',
        'order',
    ];

    public function operationalPlan()
    {
        return $this->belongsTo(OperationalPlan::class);
    }

    public function strategicGoal()
    {
        return $this->belongsTo(OperationalPlanStrategicGoal::class);
    }

    public function items()
    {
        return $this->hasMany(OperationalPlanSummaryItem::class, 'summary_program_id');
    }
}
