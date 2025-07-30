<div x-data="{ AddColumn: false }">
    @php
        $hijri = new \Alkoumi\LaravelHijriDate\Hijri();
    @endphp

    <style>
        input::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
        }
    </style>

    @if ($isEditable)
        <div class="mb-4 flex justify-end">
            <button wire:click="toggleEditControls" type="button"
                class="rounded bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition">
                {{ $showEditControls ? __('executive-plan.hide_edit_controls') : __('executive-plan.show_edit_controls') }}
            </button>
        </div>
    @endif

    <table class="min-w-full  border border-gray-300" wire:key="table-{{ $renderKey }}">
        <thead class="bg-gray-100">
            <livewire:executive-plan.table-head :columns="$columns->toArray()" :key="$renderKey . '-' . ($showEditControls ? 'edit' : 'view')" :user="$user" :isEditable="$isEditable"
                :month="$month" :year="$year" :showEditControls="$showEditControls" />
        </thead>
        <tbody>
            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $isWeekend = false;
                @endphp

                @if (Carbon\Carbon::create($year, $month, $day)->isFriday() || Carbon\Carbon::create($year, $month, $day)->isSaturday())
                    @php
                        $isWeekend = true;
                    @endphp
                @endif



                @php
                    $date = Carbon\Carbon::create($year, $month, $day);
                @endphp

                <!-- Example rows -->
                <tr
                    class="{{ $day % 2 == 0 ? 'bg-gray-100' : '' }} hover:bg-primary-50 group transition-all ease-in-out duration-150">
                    <td style="height: 100%;"
                        class="sticky w-[7rem] border-b border-l min-h-[100px] border-r border-gray-300 flex flex-row justify-between items-center right-0 text-center  text-gray-900 border-r border-gray-300 group-hover:bg-primary-100 group-hover:text-white transition-all ease-in-out duration-150 {{ $isWeekend ? 'bg-gray-200' : 'bg-primary-50 text-white' }} ">
                        <div>
                            {{-- move up/down --}}
                            @if ($isEditable && $showEditControls)
                                <div class="flex flex-col">
                                    @if ($day != 1)
                                        <button wire:click="moveUp({{ $day }})"
                                            class="p-1 text-xs text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    @endif

                                    @if ($day != $daysInMonth)
                                        <button wire:click="moveDown({{ $day }})"
                                            class="p-1 text-xs text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="justify-between items-center  flex flex-col">
                            <span>
                                {{ $date->translatedFormat('l') }}
                            </span>
                            <span class="text-sm" title="{{ $hijri::DateIndicDigits('Y/m/d', $date) }}">
                                {{ $date->translatedFormat('Y/m/d') }}
                            </span>
                        </div>

                        <div>
                            @if ($isEditable && $showEditControls)
                                <button wire:click="deleteDay({{ $day }})"
                                    class="p-1 text-xs text-gray-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>

                    @foreach ($columns as $column)
                        @php
                            $cell = $cells->firstWhere(function ($c) use ($date, $column) {
                                return Carbon\Carbon::parse($c->date)->format('Y-m-d') == $date->format('Y-m-d') &&
                                    $c->executive_plan_column_id == $column->id;
                            });

                        @endphp


                        <livewire:executive-plan.table-cell :cell="$cell" :column="$column" :date="$date"
                            :key="$day . $column->id . $renderKey . '-' . ($showEditControls ? 'edit' : 'view')" :user="$user" :isEditable="$isEditable" :showEditControls="$showEditControls" />
                    @endforeach


                </tr>
                <!-- Repeat rows as needed -->
            @endfor

        </tbody>
    </table>



    @if ($isEditable)
        <x-modal name="AddColumn" class="hidden">
            <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                {{ __('executive-plan.add_column') }}
            </div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                <form wire:submit.prevent="addColumn">
                    @csrf
                    <div class="mb-4">
                        <label for="name"
                            class="block text-sm font-medium text-slate-700">{{ __('executive-plan.name') }}</label>
                        <input type="text" name="name" id="name" wire:model="name"
                            placeholder="{{ __('executive-plan.name') }}" required
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                    </div>
                    <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                        <button
                            class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                            type="submit">
                            {{ __('executive-plan.add') }}
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    @endif

</div>
