<div class="w-56 relative">
    <button type="button" wire:click="toggleOpen" class="w-full border rounded px-3 py-2 text-right bg-white flex items-center justify-between">
        <span>
            {{ $selected ? optional($users->firstWhere('id', $selected))->name : __('tasks.assigned_by.default') }}
        </span>
        <span class="float-left">&#9660;</span>
    </button>
    @if($open)
        <div class="absolute z-20 w-full bg-white border rounded mt-1 shadow" wire:click.away="close">
            <input type="text" wire:model.live="search" placeholder="بحث عن مُكلف..." class="w-full px-2 py-1 border-b text-sm">
            <ul class="max-h-48 overflow-y-auto">
                <li wire:click="$set('selected', '')" class="px-3 py-2 cursor-pointer hover:bg-gray-100 {{ $selected === '' ? 'bg-blue-100 font-bold' : '' }}">
                    {{ __('tasks.assigned_by.default') }}
                </li>
                @foreach($users as $user)
                    <li wire:click="$set('selected', '{{ $user->id }}')" class="px-3 py-2 cursor-pointer hover:bg-gray-100 {{ $selected == $user->id ? 'bg-blue-100 font-bold' : '' }}">
                        {{ $user->name }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <input type="hidden" wire:model="selected">
</div> 