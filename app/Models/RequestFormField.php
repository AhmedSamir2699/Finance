<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFormField extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_required',
        'options',
        'request_form_id',
        'x',
        'y',
        'order'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function form()
    {
        return $this->belongsTo(RequestForm::class);
    }
}
