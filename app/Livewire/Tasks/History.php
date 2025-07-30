<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class History extends Component
{
    public $task;
    
    public function render()
    {
        return view('livewire.tasks.history');
    }
}
