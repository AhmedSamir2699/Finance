<?php

namespace App\Livewire\FinanceItems;

use App\Models\FinanceItem;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    /** Current parent context (ID + loaded model for labels) */
    public ?int $parentId = null;
    public ?FinanceItem $parentModel = null;

    /**
     * Works for route: /finance-items or /finance-items/{parent}
     * Livewire v3 will inject $parent via route-model binding if the route param is named {parent}.
     */
    public function mount(?FinanceItem $parent = null, ?int $parentId = null): void
    {
        if ($parent) {                          // e.g., /finance-items/{parent}
            $this->parentId = $parent->id;
            $this->parentModel = $parent;
        } elseif ($parentId) {                  // fallback if you ever pass plain ID
            $this->parentId = $parentId;
            $this->parentModel = FinanceItem::find($parentId);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** In-page navigation (no URL change) */
    public function showChildren(int $id): void
    {
        $this->parentId = $id;
        $this->parentModel = FinanceItem::findOrFail($id);
        $this->resetPage();
    }

    public function clearParent(): void
    {
        $this->parentId = null;
        $this->parentModel = null;
        $this->resetPage();
    }

    function sumChildren($node): float
    {
        $sum = 0.0;
        foreach ($node->childrenRecursive as $child) {
            $sum += (float) ($child->amount ?? 0);
            $sum += $this->sumChildren($child);
        }
        return $sum;
    }
    private function sumSubtree(FinanceItem $node): float
    {
        $sum = (float) ($node->amount ?? 0);

        // ensure relation loaded (defensive)
        if (!$node->relationLoaded('childrenRecursive')) {
            $node->load('childrenRecursive');
        }

        foreach ($node->childrenRecursive as $child) {
            $sum += $this->sumSubtree($child);
        }

        return $sum;
    }
    public function render()
    {
        $query = FinanceItem::query()
            ->with(['parent'])
            ->withCount('children')
            ->withSum('children as children_amount_sum', 'amount')
            ->orderBy('id', 'desc');

        if ($this->parentId) {
            $query->where('parent_id', $this->parentId);
        } else {
            $query->whereNull('parent_id');
        }

        if (filled($this->search)) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $items = $query->paginate($this->perPage);
        $childrenTotals = [];
        $branchTotals = [];

        foreach ($items as $item) {
            // load full subtree once
            $node = FinanceItem::with('childrenRecursive')->find($item->id);

            $subtree = $this->sumSubtree($node); // self + descendants
            $self = (float) ($node->amount ?? 0);

            $childrenTotals[$item->id] = max(0, $subtree - $self);
            $branchTotals[$item->id] = $subtree;
        }


        return view('livewire.finance-items.index', compact('items', 'childrenTotals', 'branchTotals'));

    }


}
