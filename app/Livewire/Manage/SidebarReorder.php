<?php

namespace App\Livewire\Manage;

use App\Models\SidebarLink;
use Livewire\Component;

class SidebarReorder extends Component
{
    public $links = [];

    public function mount()
    {
        $this->loadLinks();
    }

    public function loadLinks()
    {
        $this->links = SidebarLink::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }

    public function render()
    {
        return view('livewire.manage.sidebar-reorder');
    }
} 