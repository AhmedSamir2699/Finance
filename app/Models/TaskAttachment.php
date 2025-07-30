<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
 protected $fillable = [
     'task_id',
     'name',
     'path',
     'type',
     'size',
     'extension',
     'mime_type',
     'user_id',
 ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getIconAttribute()
    {
        $extension = pathinfo($this->name, PATHINFO_EXTENSION);

        return match ($extension) {
            'pdf' => 'file-pdf',
            'doc', 'docx' => 'file-word',
            'xls', 'xlsx' => 'file-excel',
            'ppt', 'pptx' => 'file-powerpoint',
            'zip', 'rar', '7z' => 'file-archive',
            'jpg', 'jpeg', 'png', 'gif' => 'image',
            'mp4', 'avi', 'mkv' => 'file-video',
            'mp3', 'wav' => 'file-audio',
            default => 'file',
        };
    }

    public function getDownloadLinkAttribute()
    {
        return route('task-attachments.download', $this->id);
    }  


}
