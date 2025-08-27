<div class="flex flex-wrap -mx-6" wire:poll.15000ms>
    <div class="w-full px-6 mt-3 block">
        <a href="#" class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm hover:shadow-md">
            <div class="flex-0 py-2 px-3 bg-orange-600 bg-opacity-75 rounded-full text-center">
                <i class="fas fa-coins text-white fa-lg"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700"> {{ number_format($totalexpenses, 2)}} {{ __('global.currency') }} </h4>
                <div class="text-gray-500">{{ __('global.total_expenses') }}</div>
            </div>
        </a>
    </div>
</div>
