<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_public',
        'views',
        'candidates',
        'description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'candidates' => 'array',
    ];

    protected $appends = [
        'candidates_collection',
        'is_future',
        'is_past',
    ];

    public function votes()
    {
        return $this->hasMany(ElectionVote::class);
    }

    public function getCandidate($id)
    {
        if ($this->candidates === null) {
            return null;
        }

        if (is_string($this->candidates)) {
            $this->candidates = json_decode($this->candidates, true);
        }

        if (empty($this->candidates)) {
            return null;
        }

        $collection = collect($this->candidates);
        return $collection->firstWhere('id', $id);
    }

    public function getCandidatesCollectionAttribute()
    {
        $value = $this->attributes['candidates'] ?? null;

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return collect($decoded);
        }

        return collect();
    }

    public function getIsFutureAttribute()
    {
        return $this->start_date > now();
    }
    public function getIsPastAttribute()
    {
        return $this->end_date < now();
    }
}
