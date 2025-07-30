<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalPlanActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'activity_id',
        'completed_at',
        'notes',
    ];

    public function activity()
    {
        return $this->belongsTo(OperationalPlanActivity::class);
    }
}
