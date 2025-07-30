<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanActivity extends Model
{
    protected $fillable = [
        'subprogram_id',
        'title',
        'yearly_target',
        'notes',
    ];

    public function subprogram()
    {
        return $this->belongsTo(OperationalPlanSubProgram::class, 'sub_program_id');
    }

    public function logs()
    {
        return $this->hasMany(OperationalPlanActivityLog::class, 'activity_id');
    }
}
