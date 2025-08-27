<div class="p-4 space-y-4">
    @can('income_create')
        <a href="{{ route('admin.incomes.create') }}"
            class="inline-block px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded shadow">
            {{ __('global.add') }} {{ __('cruds.income.title_singular') }}
        </a>
    @endcan

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
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="rounded bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition"
                href="{{ route('incomes.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.income.title_singular') }}
            </a>
        </div>
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
    </div> --}}

    <script>
        Livewire.on('departmentSelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('department', id);
        });
        Livewire.on('assignedBySelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('assignedBy', id);
        });
    </script>
    <div class="overflow-auto rounded border">
        <table class="min-w-full text-sm text-right">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-2">{{ __('cruds.income.fields.id') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.income.fields.income_category') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.income.fields.entry_date') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.income.fields.amount') }}</th>
                    {{-- <th class="px-4 py-2">{{ __('cruds.income.fields.description') }}</th> --}}
                    {{-- NEW: assigned items & residual --}}
                    <th class="px-4 py-2">البنود المالية</th>
                    <th class="px-4 py-2">المتبقي</th>
                    <th class="px-4 py-2">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $income->id }}</td>
                        <td class="px-4 py-2">{{ $income->income_category->name ?? '' }}</td>
                        <td class="px-4 py-2">{{ $income->entry_date ?? '' }}</td>
                        <td class="px-4 py-2">{{ number_format($income->amount, 2)  ?? '' }} {{ __('global.currency') }}</td>
                        {{-- <td class="px-4 py-2">{{ $income->description ?? '' }}</td> --}}
                        {{-- NEW: Assigned Items --}}
                        <td class="px-4 py-2">
                            @if ($income->allocations->isEmpty())
                                <span class="text-gray-400">—</span>
                            @else
                                <ul class="space-y-1">
                                    @foreach ($income->allocations as $a)
                                        <li class="leading-tight">
                                            <span class="font-medium">
                                                {{ $a->financeItem->name ?? '#' . $a->finance_item_id }}
                                            </span>
                                            <span class="text-gray-500">
                                                ({{ rtrim(rtrim(number_format($a->percentage, 3), '0'), '.') }}%) {{ __('global.currency') }}
                                            </span>
                                            <span>— {{ number_format($a->amount, 2) }} {{ __('global.currency') }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>

                        {{-- NEW: Residual (unallocated) --}}
                        <td class="px-4 py-2">
                            @php
                                // If you added the accessor:
                                $residual =
                                    $income->unallocated_amount ??
                                    (float) $income->amount - (float) $income->allocations->sum('amount');
                            @endphp
                            <span class="{{ $residual > 0 ? 'text-amber-700' : 'text-gray-700' }}">
                                {{ number_format(max($residual, 0), 2) }} {{ __('global.currency') }}
                            </span>
                        </td>
                        <td class="px-4 py-2 space-x-1">
                            {{-- @can('income_show')
                                <a href="{{ route('incomes.show', $income) }}"
                                    class="text-blue-500 hover:underline">{{ __('global.view') }}</a>
                            @endcan --}}
                            {{-- @can('income_edit') --}}
                            <a href="{{ route('incomes.edit', $income) }}"
                                class="text-yellow-600 hover:underline">{{ __('global.edit') }}
                            </a>
                            {{-- @endcan --}}
                            {{-- @can('income_delete') --}}
                            <form action="{{ route('incomes.destroy', $income->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">
                                    {{ __('global.delete') }}
                                </button>
                            </form>

                            {{-- @endcan --}}
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

    <div>{{ $incomes->links() }}</div>
</div>
