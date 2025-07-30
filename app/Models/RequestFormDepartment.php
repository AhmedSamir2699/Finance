<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFormDepartment extends Model
{
    protected $fillable = [
        'department_id',
        'request_form_id',
    ];

    public function form()
    {
        return $this->belongsTo(RequestForm::class,
            'request_form_id',
            'id'
        );
    }

    public function department()
    {
        return $this->belongsTo(Department::class,
            'department_id',
            'id'
        );
    }
}
