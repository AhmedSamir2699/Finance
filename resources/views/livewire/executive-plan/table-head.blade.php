<tr class="">
    <th class="min-w-[120px] bg-primary-200 font-semibold text-white border-b border-r border-gray-50">
        {{ __('executive-plan.day') }}
    </th>
    @php
        $orders = collect($columns)->pluck('order');
        $minOrder = $orders->min();
        $maxOrder = $orders->max();
    @endphp
    @foreach ($columns as $column)
        <th
        style="min-width: 200px; text-break: word-wrap;"
        class="sticky top-0 bg-primary-200 font-semibold text-white border-b border-r border-b-gray-400 border-r-gray-50 min-w-[120px] w-auto text-wrap">
            <div class="flex flex-row justify-between items-center gap-x-3">
                <div>
                    @if ($showEditControls && $isEditable && $column['order'] > $minOrder)
                        <a href="#" wire:click="orderUp({{ $column['id'] }})"
                            class="text-white hover:text-primary-100">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
                <div></div>
                <div>
                    <textarea type="text" wire:model.lazy="columns.{{ $loop->index }}.name"
                        style="min-width: 200px; text-break: word-wrap;"
                        class=" block w-full text-wrap bg-transparent border-0 text-center focus:border-1 focus:bg-white focus:ring focus:ring-1 focus:ring-primary-100 focus:text-primary-base"
                        value="" {{ (!$isEditable || !$showEditControls) ? 'disabled' : '' }}></textarea>
                </div>
                @if ($showEditControls && $isEditable)
                    <div>
                        <div>
                            <!-- Delete Button -->
                            <button type="button"
                                wire:click="deleteColumn({{ $column['id'] }})"
                                class="text-red-400 hover:text-red-700 focus:outline-none focus:ring focus:ring-red-500">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
                <div>
                    @if ($showEditControls && $isEditable && $column['order'] < $maxOrder)
                        <a href="#" wire:click="orderDown({{ $column['id'] }})"
                            class="text-white hover:text-primary-100">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </th>
    @endforeach
    @if ($showEditControls && $isEditable)
        <th style="min-width: 200px"
            class="bg-primary-200 px-4 font-semibold text-white border-b border-r border-gray-50 cursor-pointer">
            <a class="text-white" @click="AddColumn = true">
                {{ __('executive-plan.add_column') }} <i class="fas fa-plus"></i>
            </a>
        </th>
    @endif
</tr>
