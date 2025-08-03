<li class="dd-item" data-id="{{ $item->id }}">
    <div class="flex items-center justify-between bg-gray-100 px-4 py-2 rounded border border-gray-300 shadow-sm">
        <div class="dd-handle flex-1 cursor-move text-gray-800">
            {{ $item->name }} - {{ $item->amount }} {{ trans('global.currency') }}
        </div>
        {{-- Delete icon (outside dd-handle to prevent drag issues) --}}
        <button type="button"
            class="delete-item text-red-600 hover:text-red-800 text-xl font-bold px-2"
            data-id="{{ $item->id }}"
            title="حذف">
            &times;
        </button>
    </div>
        @if ($item->children->count())
            <ol class="dd-list">
                @foreach ($item->children as $child)
                    @include('admin.financeItems.partials.item', ['item' => $child])
                @endforeach
            </ol>
        @endif
    {{-- @include('admin.financeItems.partials.edit_modal', ['item' => $item, 'allItems' => $allItems]) --}}
</li>
