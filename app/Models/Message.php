<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',

        'subject',
        'content',

        'reply_to',

        'sent_at',
        'thread_id'
    ];

    protected $dates = [
        'sent_at',
    ];


    public function from()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'message_recipients', 'message_id', 'user_id')
                        ->withPivot('is_read', 'read_at');
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to');
    }

    public function attachedFiles()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    function addRecipient($user)
    {
        
        $this->recipients()->detach($user);
        $this->recipients()->attach($user);
        
    }
    
    public function thread()
    {
        return $this->hasMany(Message::class, 'thread_id')->with(['recipients', 'from']);
    }

    public function getIsReadAttribute()
    {
        return $this->recipients()->where('user_id', auth()->id())->first()->pivot->is_read;
    }



    public function scopeUnread($query)
    {
        return $query->whereHas('recipients', function($query) {
            $query->where('user_id', auth()->id())
                  ->where('is_read', 0);
        });
    }

    public function scopeSent($query)
    {
        return $query->where('sender_id', auth()->id());
    }

    public function scopeReceived($query)
    {
        return $query->whereHas('recipients', function($query) {
            $query->where('user_id', auth()->id());
        });
    }




}
