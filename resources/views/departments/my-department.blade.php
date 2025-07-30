<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto" >


@can('department.view-members')
        <div class="flex flex-col mb-4" x-data="{ expand: false }">
            <h3 class="text-2xl font-semibold text-gray-700 inline-block cursor-pointer"  @click="expand = !expand">
                {{ __('departments.members.label') }}
                <i class="fas fa-sm my-5 text-primary-500" :class="expand ? 'fa-chevron-down' : 'fa-chevron-left'"></i>
            </h3>
            <div x-show="expand">
                <livewire:departments.members-table :department="$department" />
            </div>
        </div>
@endcan

@can(['task.index-department'])
        <div class="flex flex-col" x-data="{ expand: false }">
            <h3 class="text-2xl font-semibold text-gray-700 inline-block cursor-pointer"  @click="expand = !expand">
                {{ __('tasks.department.tasks') }}
                <i class="fas fa-sm my-5 text-primary-500" :class="expand ? 'fa-chevron-down' : 'fa-chevron-left'"></i>
            </h3>
            <div x-show="expand">
            <livewire:tasks.table-list :department="$department" />
            </div>
        </div>
@endcan
    </div>

</x-app-layout>
