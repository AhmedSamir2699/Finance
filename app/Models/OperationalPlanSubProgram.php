<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanSubProgram extends Model
{
    protected $fillable = [
        'program_id',
        'title',
        'sub_budget',
        'order',
    ];

    public function program()
    {
        return $this->belongsTo(OperationalPlanProgram::class);
    }

    public function items()
    {
        return $this->hasMany(OperationalPlanItem::class, 'sub_program_id');
    }

    public function activities()
    {
        return $this->hasMany(OperationalPlanActivity::class, 'sub_program_id');
    }

    public function getTotalBudgetAttribute()
    {
        return $this->items->sum('total_cost');
    }
    public function getActivitiesCountAttribute()
    {
        return $this->items->count() + $this->activities->count();
    }
}
