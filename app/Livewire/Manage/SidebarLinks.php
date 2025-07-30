<?php

namespace App\Livewire\Manage;

use App\Models\SidebarLink;
use Livewire\Component;
use Livewire\Attributes\On;

class SidebarLinks extends Component
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
            ->get()
            ->toArray();
    }

    public function toggleStatus($linkId)
    {
        $link = SidebarLink::find($linkId);
        if ($link) {
            $link->update(['is_active' => !$link->is_active]);
            $this->loadLinks();
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => __('sidebar.messages.status_updated')
            ]);
        }
    }

    public function deleteLink($linkId)
    {
        $link = SidebarLink::find($linkId);
        if ($link) {
            $link->delete();
            $this->loadLinks();
            
            // Use toastr helper for success message
            flash()->success(__('sidebar.messages.deleted'));
        }
    }

    public function render()
    {
        return view('livewire.manage.sidebar-links');
    }
} 