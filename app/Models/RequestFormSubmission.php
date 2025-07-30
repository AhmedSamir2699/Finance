<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFormSubmission extends Model
{
    protected $fillable = ['request_form_id', 'user_id', 'status', 'fields', 'current_step', 'steps'];

    protected $casts = [
        'fields' => 'array',
        'steps' => 'array'
    ];

    public function requestForm()
    {
        return $this->belongsTo(RequestForm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(RequestFormPathSteps::class, 'current_step');
    }

    public function nextStep()
    {
        $nextStep = $this->step->nextStep();
        $this->current_step = $nextStep->id;
        $this->save();
    }

    public function previousStep()
    {
        $previousStep = $this->step->previousStep();
        $this->current_step = $previousStep->id;
        $this->save();
    }

    public function approve()
    {
        $this->status = 'approved';
        $this->save();
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }
}
