<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutivePlanColumn extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'month',
        'year',
        'name',
        'order',
    ];

    public function cells()
    {
        return $this->hasMany(ExecutivePlanCell::class);
    }
}
