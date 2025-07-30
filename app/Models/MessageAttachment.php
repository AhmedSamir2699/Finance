<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function getSizeAttribute($value)
    {
        $size = number_format($value / 1024, 2);
        return $size > 1024 ? $size . ' MB' : $size . ' KB';
    }
}
