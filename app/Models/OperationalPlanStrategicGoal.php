<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanStrategicGoal extends Model
{
    protected $fillable = [
        'operational_plan_id',
        'operational_plan_department_id',
        'operational_plan_program_id',
        'description',
        'title',
        'order',
    ];

    public function operationalPlan()
    {
        return $this->belongsTo(OperationalPlan::class);
    }

    public function programs()
    {
        return $this->hasMany(OperationalPlanProgram::class, 'operational_plan_strategic_goal_id');
    }

    public function summaryPrograms()
    {
        return $this->hasMany(OperationalPlanSummaryProgram::class);
    }
}
