<?php

namespace App\Livewire\ExpenseCategories;

use App\Models\ExpenseCategory;
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
        $expenseCategories = ExpenseCategory::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.expense-categories.index', compact('expenseCategories'));
    }
}
