<?php

namespace App\Livewire\Messages;

use App\Models\Message;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;


class Index extends Component
{
    use WithPagination;
    
    public $currentTab = 'inbox';
    public $perPage = 10;
    public $messageOpen = false;
    public $message;

    public function changeTab($tab)
    {
        $this->currentTab = $tab;
        $this->messageOpen = false;
    }

    

    public function readMessage(Message $message)
    {
        $this->messageOpen = true;
        $this->message = $message;
    }
    public function render()
    {

            $messages = Message::query();

            if($this->currentTab == 'inbox') {
                $messages = $messages->received();
            } else {
                $messages = $messages->sent();
            }

            $messages = $messages->latest()->paginate($this->perPage);


        return view('livewire.messages.index',
    [
        'messages' => $messages
    ]);
    }
}
