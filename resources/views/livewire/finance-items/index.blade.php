<div class="p-4 space-y-4">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            function initTomSelects() {
                document.querySelectorAll('.searchable-select').forEach(function(el) {
                    if (!el.tomselect) {
                        new TomSelect(el, {
                            create: false,
                            allowEmptyOption: true,
                            persist: false
                        });
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', initTomSelects);
            document.addEventListener('livewire:update', initTomSelects);
        </script>
    @endpush
    <div class="p-4 space-y-4">

        {{-- Add button --}}
        <div class="row mb-2">
            <div class="col-lg-12">
                <a class="rounded bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition"
                    href="{{ route('finance-items.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.financeItem.title_singular') }}
                </a>
            </div>
        </div>

        {{-- Context header --}}
          @if($this->parentId)
            <div class="flex items-center justify-between bg-white rounded shadow p-3">
                <div class="text-sm">
                    <span class="text-gray-500">عرض تفاصيل</span>
                    <span class="font-semibold">{{ $this->parentModel->name }}</span>
                </div>
                <button wire:click="clearParent" class="text-blue-600 hover:underline text-sm">
                    ← الرجوع للخلف
                </button>
            </div>
        @endif
    </div>
    {{-- <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.status.label') ?? 'الحالة' }}</label>
                <select wire:model.live="status" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[120px]">
                    <option value="">{{ __('tasks.status.default') }}</option>
                    <option value="pending">{{ __('tasks.status.pending') }}</option>
                    <option value="in_progress">{{ __('tasks.status.in_progress') }}</option>
                    <option value="submitted">{{ __('tasks.status.submitted') }}</option>
                    <option value="approved">{{ __('tasks.status.completed') }}</option>
                    <option value="overdue">{{ __('tasks.status.overdue') }}</option>
                    @can('task.view-trashed')
                        <option value="deleted">{{ __('tasks.status.deleted') }}</option>
                    @endcan
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.priority.label') ?? 'الأولوية' }}</label>
                <select wire:model.live="priority" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[120px]">
                    <option value="">{{ __('tasks.priority.default') }}</option>
                    <option value="low">{{ __('tasks.priority.low') }}</option>
                    <option value="medium">{{ __('tasks.priority.medium') }}</option>
                    <option value="high">{{ __('tasks.priority.high') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">من التاريخ</label>
                <input type="date" wire:model.live="fromDate" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="من التاريخ">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">إلى التاريخ</label>
                <input type="date" wire:model.live="toDate" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="إلى التاريخ">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.search.label') ?? 'بحث' }}</label>
                <input type="text" wire:model.live="search" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="{{ __('tasks.search.placeholder') }}">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">عدد النتائج</label>
                <select wire:model.live="perPage" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[80px]">
                    <option>5</option>
                    <option>10</option>
                    <option>15</option>
                    <option>20</option>
                </select>
            </div>
        </div>
    </div> 

    <script>
        Livewire.on('departmentSelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('department', id);
        });
        Livewire.on('assignedBySelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('assignedBy', id);
        });
    </script>
    --}}
    <div class="overflow-auto rounded border">
        <table class="min-w-full text-sm text-right">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-2">{{ __('cruds.financeItem.fields.id') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.financeItem.fields.name') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.financeItem.fields.parent') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.financeItem.fields.amount') }}</th>
                    <th class="px-4 py-2">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->id }}</td>
                        <td class="px-4 py-2">
                            @if ($item->children_count > 0)
                                <a href="#" wire:click.prevent="showChildren({{ $item->id }})"
                                    class="text-blue-600 hover:underline">
                                    {{ $item->name }}
                                </a>
                            @else
                                <span>{{ $item->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $item->parent->name ?? '' }}</td>
                        <td class="px-4 py-2">{{ number_format($branchTotals[$item->id] ?? 0, 0) }}</td>
                        <td class="px-4 py-2 space-x-1">
                            <a href="{{ route('finance-items.edit', $item) }}"
                                class="text-yellow-600 hover:underline">{{ __('global.edit') }}
                            </a>
                            <form action="{{ route('finance-items.destroy', $item->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">
                                    {{ __('global.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">
                            {{ __('global.no_data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $items->links() }}</div>
</div>
