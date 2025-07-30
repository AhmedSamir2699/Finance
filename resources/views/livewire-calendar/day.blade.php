<div ondragenter="onLivewireCalendarEventDragEnter(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragleave="onLivewireCalendarEventDragLeave(event, '{{ $componentId }}', '{{ $day }}', '{{ $dragAndDropClasses }}');"
    ondragover="onLivewireCalendarEventDragOver(event);"
    ondrop="onLivewireCalendarEventDrop(event, '{{ $componentId }}', '{{ $day }}', {{ $day->year }}, {{ $day->month }}, {{ $day->day }}, '{{ $dragAndDropClasses }}');"
    class="group/day flex-0 h-40 lg:h-48 border border-gray-200 -mt-px -ml-px transition ease-in-out" style="width: 20%;">

    {{-- Wrapper for Drag and Drop --}}
    <div class="w-full h-full" id="{{ $componentId }}-{{ $day }}">

        <div @if ($dayClickEnabled) wire:click="onDayClick({{ $day->year }}, {{ $day->month }}, {{ $day->day }})"
                @endif
            class="w-full h-full p-2 cursor-pointer {{ $dayInMonth ? ($isToday ? 'bg-yellow-100' : ' bg-white hover:bg-primary-300 hover:shadow-lg transition duration-200 ease-in-out ') : 'bg-gray-100' }} flex flex-col">

            {{-- Number of Day --}}
            <div class="flex items-center">
                <p class="text-sm {{ $dayInMonth ? ' font-medium ' : '' }}  {{ !$isToday ? 'group-hover/day:text-white' : '' }}">
                    {{ $day->format('j') }}
                </p>
            </div>

            {{-- Events --}}
                <div class="p-2 my-2 flex-1 overflow-auto">
                    <div class="grid grid-cols-1 grid-flow-row gap-2">
                        @foreach ($events as $event)
                            <div @if ($dragAndDropEnabled) draggable="true" @endif
                                ondragstart="onLivewireCalendarEventDragStart(event, '{{ $event['id'] }}')">
                                @include($eventView, [
                                    'event' => $event,
                                ])
                            </div>
                        @endforeach

                    </div>
                </div>
            @if (!count($events))
                <div class="text-white group-hover/day:opacity-100 opacity-0 text-center w-full h-full">
                    <i class="fas fa-plus fa-2x mt-[12%]"></i>
                </div>
            @endif
        </div>
    </div>
</div>
