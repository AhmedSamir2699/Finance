<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto" >

        <div class="mt-1">
            <livewire:tasks.executive-plan.tasks-cards />
        </div>
        <div class="mt-8">

        </div>

        <div class="flex flex-col">
            <livewire:tasks.executive-plan.table-list />
        </div>

    </div>

</x-app-layout>
