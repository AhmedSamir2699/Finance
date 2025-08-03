<?php

namespace App\Livewire\Incomes;


use App\Models\Income;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
     use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function render()
    {
        $incomes = Income::query()
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.incomes.index', compact('incomes'));
    }
}
