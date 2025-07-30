<?php

namespace App\Livewire\Messages;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadAttachments extends Component
{
    use WithFileUploads;

    public $attachments = [];
    public $uploadedAttachments = [];

    public function updatedAttachments($files)
    {

        foreach ($files as $file) {
            $icon = $file->getClientOriginalExtension();
            // fontawesome icons
            switch ($icon) {
                case 'pdf':
                    $icon = 'file-pdf';
                    break;
                case 'doc':
                case 'docx':
                    $icon = 'file-word';
                    break;
                case 'xls':
                case 'xlsx':
                    $icon = 'file-excel';
                    break;
                case 'ppt':
                case 'pptx':
                    $icon = 'file-powerpoint';
                    break;
                case 'zip':
                case 'rar':
                case '7z':
                    $icon = 'file-archive';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    $icon = 'image';
                    break;
                case 'mp4':
                case 'avi':
                case 'mkv':
                    $icon = 'file-video';
                    break;
                case 'mp3':
                case 'wav':
                    $icon = 'file-audio';
                    break;
                default:
                    $icon = 'file';
                    break;
            }
            $this->uploadedAttachments[] = [
                'filename' => $file->getClientOriginalName(),
                'icon' => $icon,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ];
        }
    }

    public function removeAttachment($index)
    {
        unset($this->uploadedAttachments[$index]);
        unset($this->attachments[$index]);
    }

    public function render()
    {
        return view('livewire.messages.upload-attachments');
    }
}
