<x-app-layout>
    <div class="flex flex-col mt-8">
        <livewire:tasks.my-tasks-calendar 
        event-view="livewire-calendar.event"
        day-of-week-view="livewire-calendar.day-of-week"
        day-view="livewire-calendar.day"
        calendar-view="livewire-calendar.calendar"
        pollMillis="15000"
        />
    </div>
</x-app-layout>