<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto" >

        <div class="mt-1 mb-4">
            <livewire:tasks.tasks-cards />
        </div>
        <div class="flex flex-col">
            <livewire:tasks.table-list />
        </div>

    </div>

</x-app-layout>
