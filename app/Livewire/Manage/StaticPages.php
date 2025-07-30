<?php

namespace App\Livewire\Manage;

use App\Models\StaticPage;
use Livewire\Component;
use Livewire\WithPagination;

class StaticPages extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $visibility = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'visibility' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingVisibility()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function copyLink($pageId)
    {
        $page = StaticPage::find($pageId);
        if ($page) {
            $this->dispatch('copyToClipboard', $page->copy_link);
            toastr()->success(__('static-pages.messages.link_copied'));
        }
    }

    public function toggleStatus($pageId)
    {
        $page = StaticPage::find($pageId);
        if ($page) {
            $page->update(['is_active' => !$page->is_active]);
            $message = $page->is_active 
                ? __('static-pages.messages.activated')
                : __('static-pages.messages.deactivated');
            toastr()->success($message);
        }
    }

    public function deletePage($pageId)
    {
        $page = StaticPage::find($pageId);
        if ($page) {
            $page->delete();
            toastr()->success(__('static-pages.messages.deleted'));
        }
    }

    public function render()
    {
        $pages = StaticPage::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->when($this->visibility !== '', function ($query) {
                $query->where('visibility', $this->visibility);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.manage.static-pages', compact('pages'));
    }
} 