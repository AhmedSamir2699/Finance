<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'criteria_id', 
        'score',
        'evaluated_at'
    ];

    protected $casts = [
        'score' => 'integer',
        'evaluated_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function criteria()
    {
        return $this->belongsTo(EvaluationCriteria::class, 'criteria_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('evaluated_at', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('evaluated_at', [$startDate, $endDate]);
    }
} 