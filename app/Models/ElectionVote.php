<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVote extends Model
{
    protected $fillable = [
        'election_id',
        'name',
        'nin',
        'candidate_id',
        'ip_address',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $appends = [
        'candidate',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
    
    public function getCandidateAttribute()
    {
        return $this->election->getCandidate($this->candidate_id);
    }
}
