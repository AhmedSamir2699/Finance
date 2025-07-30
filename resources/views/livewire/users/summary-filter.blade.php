<div class="w-2/3 bg-gradient-to-r from-primary-100 to-primary-200 border border-blue-200 rounded-xl p-6 my-6 shadow-lg">
    <div class="flex flex-wrap items-end gap-6">
        <!-- Time Period Shortcuts -->
        <div class="flex flex-col gap-2 min-w-[180px]">
            <label for="time_period" class="text-sm font-semibold text-white flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                {{ __('users.summary.time_period') }}
            </label>
            <select wire:model.live="timePeriod" id="time_period" class="rounded-lg border-2 border-blue-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 text-sm bg-white shadow-sm transition-all duration-200">
                <option value="">{{ __('users.summary.custom_range') }}</option>
                <option value="today">{{ __('users.summary.today') }}</option>
                <option value="last_week">{{ __('users.summary.last_week') }}</option>
                <option value="last_month">{{ __('users.summary.last_month') }}</option>
                <option value="last_year">{{ __('users.summary.last_year') }}</option>
            </select>
        </div>

        <!-- Date Range Fields -->
        <div class="flex flex-col gap-2 min-w-[160px]">
            <label for="start_date" class="text-sm font-semibold text-white flex items-center gap-2">
                <i class="fas fa-calendar-day"></i>
                {{ __('users.summary.from') }}
            </label>
            <input type="date" wire:model="startDate" id="start_date" 
                class="rounded-lg border-2 border-green-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 text-sm bg-white shadow-sm transition-all duration-200">
        </div>

        <div class="flex flex-col gap-2 min-w-[160px]">
            <label for="end_date" class="text-sm font-semibold text-white flex items-center gap-2">
                <i class="fas fa-calendar-day"></i>
                {{ __('users.summary.to') }}
            </label>
            <input type="date" wire:model="endDate" id="end_date" 
                class="rounded-lg border-2 border-red-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 text-sm bg-white shadow-sm transition-all duration-200">
        </div>

        <div class="flex items-end">
            <button type="button" wire:click="filter"
                class="bg-primary-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-all duration-200 transform hover:scale-105 flex items-center gap-2">
                <i class="fas fa-filter"></i>
                {{ __('users.summary.filter') }}
            </button>
        </div>
    </div>
</div> 