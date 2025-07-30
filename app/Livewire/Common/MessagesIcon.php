<?php

namespace App\Livewire\Common;

use Livewire\Component;

class MessagesIcon extends Component
{
    public $hasNewMessages = false;

    public function render()
    {

        $this->hasNewMessages = auth()->user()->unreadMessages->count() > 0;
        return view('livewire.common.messages-icon');
    }
}
