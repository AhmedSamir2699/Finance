<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanDepartment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'operational_plan_id',
    ];

    public function operationalPlan()
    {
        return $this->belongsTo(OperationalPlan::class);
    }
    public function strategicGoals()
    {
        return $this->hasMany(OperationalPlanStrategicGoal::class);
    }
    public function programs()
    {
        return $this->hasMany(OperationalPlanProgram::class);
    }
}
