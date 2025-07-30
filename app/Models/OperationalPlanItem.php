<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanItem extends Model
{
    protected $fillable = [
        'sub_program_id',
        'title',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    public function subProgram()
    {
        return $this->belongsTo(OperationalPlanSubProgram::class);
    }

    public function activities()
    {
        return $this->hasMany(OperationalPlanActivity::class, 'item_id');
    }
}
