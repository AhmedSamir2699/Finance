<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFormPathSteps extends Model
{
    protected $fillable = [
        'department_id',
        'user_id',
        'role_id',
        'step_order',
    ];

    public function form()
    {
        return $this->belongsTo(RequestForm::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
