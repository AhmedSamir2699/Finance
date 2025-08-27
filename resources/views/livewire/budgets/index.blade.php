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

        {{-- Context header --}}
        @if ($this->parentFiId)
            <div class="flex items-center justify-between bg-white rounded shadow p-3">
                <div class="text-sm">
                    <span class="text-gray-500">عرض تفاصيل</span>
                    <span class="font-semibold">{{ $this->parentFiModel?->name }}</span>
                </div>
                <button wire:click="goBackOneLevel" class="text-blue-600 hover:underline text-sm">
                    ← الرجوع للخلف
                </button>

            </div>
        @endif

    </div>
    <div class="overflow-auto rounded border">
        <table class="min-w-full text-sm text-right">
            <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-2">{{ __('cruds.budgets.fields.id') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.budgets.fields.name') }}</th>
                    <th class="px-4 py-2">{{ __('cruds.budgets.fields.parent') }}</th>
                    <th class="px-4 py-2">الواقع</th>
                    <th class="px-4 py-2">الهدف</th>
                    <th class="px-4 py-2">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $fi = $item->financeItem; // shorthand
                        $hasChildren = $fi && $fi->children && $fi->children->count() > 0;
                    @endphp

                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->id }}</td>

                        {{-- NAME (now from finance_items) --}}
                        <td class="px-4 py-2">
                            @if ($hasChildren)
                                <a href="#" wire:click.prevent="showChildrenFi({{ $fi->id }})"
                                    class="text-blue-600 hover:underline">
                                    {{ $fi->name ?? '—' }}
                                </a>
                            @else
                                <span>{{ $fi->name ?? '—' }}</span>
                            @endif
                        </td>

                        {{-- PARENT (from finance_items.parent) --}}
                        <td class="px-4 py-2">{{ optional($fi->parent)->name ?? '' }}</td>

                        {{-- AMOUNT (branch total of the finance item tree) --}}
                        <td class="px-4 py-2">
                            {{ number_format($branchTotals[$item->id] ?? 0, 0) }} {{ __('global.currency') }}
                        </td>

                        <td class="px-4 py-2">
                            {{ number_format($item->goal ?? 0, 0) }} {{ __('global.currency') }}
                        </td>

                        <td class="px-4 py-2 space-x-1">
                            <a href="{{ route('budgets.edit', $item) }}" class="text-yellow-600 hover:underline">
                                {{ __('global.edit') }}
                            </a>
                            <form action="{{ route('budgets.destroy', $item->id) }}" method="POST"
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
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            {{ __('global.no_data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div>{{ $items->links() }}</div>
</div>
