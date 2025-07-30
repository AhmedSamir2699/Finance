<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestForm extends Model
{
    protected $fillable = [
        'title',
        'description',
        'background',
        'is_active',
        'request_form_category_id'
        
    ];

    public function fields()
    {
        return $this->hasMany(RequestFormField::class);
    }

    public function submissions()
    {
        return $this->hasMany(RequestFormSubmission::class);
    }
    public function category()
    {
        return $this->belongsTo(RequestFormCategory::class, 'request_form_category_id');
    }

    public function path()
    {
        return $this->hasMany(RequestFormPathSteps::class);
    }

    public function departments()
    {
        return $this->hasMany(RequestFormDepartment::class,
            'request_form_id',
            'id'
    );
    }
}
