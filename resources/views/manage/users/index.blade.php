<x-app-layout>
    
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container px-6 py-8 mx-auto">
        <h2 class="text-xl font-semibold text-gray-700 leading-tight">{{__('users.index.headline')}} ({{$usersCount}})</h2>

        <livewire:users.table-list />

    </div>

</x-app-layout>
