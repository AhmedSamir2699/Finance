<div class="p-6 flex flex-col md:flex-row items-stretch h-full">
    <!-- Welcome Message (2/3) -->
    <div class="w-full md:w-2/3 flex flex-col justify-center md:pr-8 mb-6 md:mb-0">
        <h4 class="text-2xl font-bold text-primary-base mb-2">{{ $greeting }}, {{ $user->firstname }}!</h4>
        <p class="text-lg text-gray-700 mb-2">{{ __('dashboard.welcome_message') }}</p>
        <div class="text-sm text-gray-500 flex flex-col md:flex-row md:space-x-4 md:space-x-reverse">
            <span class="mb-1 md:mb-0">{{ $gregorianDate }}</span>
            <span>|</span>
            <span>{{ $hijriDate }}</span>
        </div>
    </div>
    <!-- Clock and Check-in Info (1/3) -->
    <div class="w-full md:w-2/3 flex flex-col items-center justify-center">
        <div class="mb-2 text-primary-base text-lg font-semibold">{{ $dayName }}</div>
        <div x-data="{ time: new Date().toLocaleTimeString('ar-SA') }" x-init="setInterval(() => { time = new Date().toLocaleTimeString('ar-SA') }, 1000)" class="mb-4">
            <div x-text="time" class="text-4xl font-bold text-gray-800"></div>
        </div>
    </div>
</div>
