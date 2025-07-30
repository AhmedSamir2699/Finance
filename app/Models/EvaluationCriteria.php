<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluationCriteria extends Model
{
    use HasFactory;

    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'name',
        'description', 
        'min_value',
        'max_value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_value' => 'integer',
        'max_value' => 'integer',
    ];

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class, 'criteria_id');
    }

    public function getActiveCriteria()
    {
        return static::where('is_active', true)->orderBy('name')->get();
    }

    public function getScoreRange()
    {
        return "{$this->min_value} - {$this->max_value}";
    }
} 