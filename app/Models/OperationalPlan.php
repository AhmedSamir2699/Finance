<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlan extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_public',
        'views',
        'period',
    ];

    public function departments()
    {
        return $this->hasMany(OperationalPlanDepartment::class);
    }

    public function summaryPrograms()
    {
        return $this->hasMany(OperationalPlanSummaryProgram::class);
    }
}
