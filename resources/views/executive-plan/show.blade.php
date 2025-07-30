<x-app-layout>
    <style>
        .scrollbar-custom::-webkit-scrollbar {
            width: 16px;
            /* Width for vertical scrollbar */
            height: 16px;
            /* Height for horizontal scrollbar */
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background-color: #035944;
            /* Customize the scrollbar color */
            border-radius: 8px;
            /* Make scrollbar rounded */
            border: 4px solid transparent;
            /* Adds space around thumb */
            background-clip: content-box;
            /* Prevents thumb from overflowing */
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background-color: #ccebe4;
            /* Track color */
            border-radius: 8px;
        }
    </style>
    <div class="mt-1">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        <div class="mt-5 overflow-x-auto h-[40rem] overflow-y-auto scrollbar-custom">

            <livewire:executive-plan.table :user="$user" :columns="$columns" :cells="$cells" :isEditable="$isEditable"
                :month="$currentMonth" :year="$currentYear" :daysInMonth="$daysInMonth" />

        </div>

    </div>

</x-app-layout>
