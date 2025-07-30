<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope_type',
        'scope_id',
        'late_arrival_tolerance',
        'early_leave_tolerance',
    ];
}
