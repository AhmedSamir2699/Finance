<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFormCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    public function forms()
    {
        return $this->hasMany(RequestForm::class);
    }
}
