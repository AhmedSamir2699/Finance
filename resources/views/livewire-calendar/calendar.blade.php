<div
    @if($pollMillis !== null && $pollAction !== null)
        wire:poll.{{ $pollMillis }}ms="{{ $pollAction }}"
    @elseif($pollMillis !== null)
        wire:poll.{{ $pollMillis }}ms
    @endif

    x-data="{ CreateEventModal: $wire.entangle('CreateEventModal'), EditEventModal: $wire.entangle('EditEventModal') }" 
>
    <div>
        @includeIf($beforeCalendarView)
    </div>

    <div class="flex">
        <div class="overflow-x-auto w-full">
            <div class="inline-block min-w-full overflow-hidden border border-1">

                <div class="w-full flex flex-row">
                    @foreach($monthGrid->first() as $day)
                        @include($dayOfWeekView, ['day' => $day])
                    @endforeach
                </div>

                @foreach($monthGrid as $week)
                    <div class="w-full flex flex-row">
                        @foreach($week as $day)
                            @if($day->isSameMonth($startsAt))
                                @include($dayView, [
                                        'isLast' => $loop->last,
                                        'componentId' => $componentId,
                                        'day' => $day,
                                        'dayInMonth' => $day->isSameMonth($startsAt),
                                        'isToday' => $day->isToday(),
                                        'events' => $getEventsForDay($day, $events),
                                    ])
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div>
        @includeIf($afterCalendarView)
    </div>

    <x-modal name="CreateEventModal">
        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
            {{ __('calendar.event.create.modal.title') }}
        </div>
        <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
            <form wire:submit="create()">
                @csrf
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm font-medium text-slate-700">{{ __('calendar.event.title') }}</label>
                    <input type="text" name="title" id="title" placeholder="{{ __('calendar.event.title') }}"
                        required
                        wire:model="title"
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                </div>
                <div class="mb-4">
                    <label for="description"
                        class="block text-sm font-medium text-slate-700">{{ __('calendar.event.description') }}</label>
                    <input type="text" name="description" id="description"
                        placeholder="{{ __('calendar.event.description') }}" required
                        wire:model="description"
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                </div>

                <input type="hidden" name="task_date" id="date"
                wire:model="task_date"
                />

                {{-- assign to someone --}}
                @can('task.assign')
                    <div class="mb-4">
                        <label for="assignee"
                            class="block text-sm font-medium text-slate-700">{{ __('calendar.event.assign') }}</label>
                        <select wire:model="assign" name="assignee" id="assignee"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="">{{ __('calendar.event.select_assignee') }}</option>
                            @foreach(auth()->user()->department->users as $user)
                                @if($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="mb-4">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('calendar.event.create') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="EditEventModal">
        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
            {{ __('calendar.event.edit.modal.title') }}
        </div>
        <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
            <form wire:submit="update({{ $taskID }})">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm font-medium text-slate-700">{{ __('calendar.event.title') }}</label>
                    <input type="text" name="title" id="title" placeholder="{{ __('calendar.event.title') }}"
                        required
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                        wire:model="title" />
                </div>
                <div class="mb-4">
                    <label for="description"
                        class="block text-sm font-medium text-slate-700">{{ __('calendar.event.description') }}</label>
                    <input type="text" name="description" id="description"
                        placeholder="{{ __('calendar.event.description') }}" required
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                        wire:model="description" />
                </div>

                @can('task.assign')
                    <div class="mb-4">
                        <label for="assignee"
                            class="block text-sm font-medium text-slate-700">{{ __('calendar.event.assign') }}</label>
                        <select name="assignee" id="assignee"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                            wire:model="assignee">
                            <option value="">{{ __('calendar.event.select_assignee') }}</option>
                            @foreach (auth()->user()->department->users as $user)
                                @if ($user->id !== auth()->id())
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="mb-4">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('calendar.event.edit') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
