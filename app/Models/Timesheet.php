<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
