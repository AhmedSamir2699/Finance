<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanProgram extends Model
{
    protected $fillable = [
        'operational_plan_strategic_goal_id',
        'operational_plan_department_id',
        'title',
        'budget',
        'order',
    ];

    public function strategicGoal()
    {
        return $this->belongsTo(OperationalPlanStrategicGoal::class);
    }

    public function subPrograms()
    {
        return $this->hasMany(OperationalPlanSubProgram::class,
            'program_id',
            'id'
        );
    }

    // add total_budget attribute
    public function getTotalBudgetAttribute()
    {
        return $this->subPrograms->sum('budget');
    }
}
