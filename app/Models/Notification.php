<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'action_route',
        'action_params',
        'is_read',
        'is_seen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->is_seen = true;
        $this->read_at = now();
        $this->save();
    }

    public function markAsSeen()
    {
        $this->is_seen = true;
        $this->save();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeUnseen($query)
    {
        return $query->where('is_seen', false);
    }

    public function getActionUrlAttribute()
    {
        if (is_null($this->action_route)) return null;
        return route($this->action_route, json_decode($this->action_params, true));
    }

}
