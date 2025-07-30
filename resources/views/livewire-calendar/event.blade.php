<div @if ($eventClickEnabled) wire:click.stop="onEventClick('{{ $event['id'] }}')" @endif
    class="group/event {{ $event['priority'] === 'low' ? 'bg-white' : ($event['priority'] === 'bg-medium-100' ? 'bg-yellow-100' : 'red') }} rounded-lg border p-1 shadow-md cursor-pointer flex flex-row justify-between">

    <p class="text-xs font-sm truncate py-2 px-1" title="{{ $event['title'] }}">
        {{ $event['title'] }}
    </p>

    <div class="flex flex-row justify-between px-1">
        <button wire:click="show('{{ $event['id'] }}')"
            class="group-hover/event:opacity-100 opacity-0 focus:outline-none border border-1 border-primary-200 p-1 text-primary-500 rounded-md w-6 hover:text-secondary-base hover:bg-primary-500">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </div>
</div>

@script
    <script>
        $wire.on('openEditEventModal', () => {
            alert(2);
            EditEventModal = true;
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('openEditEventModal', (event) => {
                alert(4);
                EditEventModal = true;
            });
        });
    </script>
@endscript
