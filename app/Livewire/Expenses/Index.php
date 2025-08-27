<?php

namespace App\Livewire\Expenses;


use App\Models\Expense;
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
        $expenses = Expense::query()
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
$totalexpenses = Expense::totalLeafAmount();
        return view('livewire.expenses.index', compact('expenses','totalexpenses'));
    }
}
