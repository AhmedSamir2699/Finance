<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutivePlanCell extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'executive_plan_column_id',
        'value',
        'description',
    ];

    public function column()
    {
        return $this->belongsTo(ExecutivePlanColumn::class, 'executive_plan_column_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
