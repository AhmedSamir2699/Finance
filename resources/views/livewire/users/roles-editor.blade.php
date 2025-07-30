<div class="mb-6 border-b py-3">
    <h4 class="text-lg font-semibold text-gray-700 leading-tight">{{ __('users.roles') }}</h4>
    @foreach ($user->roles()->get() as $role)
        <div class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-1 overflow-hidden">
            <span class="ml-2 mr-1 leading-relaxed truncate max-w-xs px-2 py-1">{{ $role->display_name }}</span>
            @if ($user->roles()->count() > 1)
                @can('user.assign-role')
                    <a href="#" wire:click="remove('{{ $role->name }}')"
                        class="w-6 h-8 inline-block text-center align-middle text-gray-500 bg-blue-200 focus:outline-none">
                        <i class="fa fa-times w-6 h-6 fill-current mx-auto mt-2"></i>
                    </a>
                @endcan
            @endif
        </div>
    @endforeach

    @can('user.assign-role')
        @if ($addingRole)
            <select wire:change="save($event.target.value)"
                class="bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-1 overflow-hidden">
                <option disabled selected>{{ __('users.edit.select_role') }}</option>
                @foreach ($roles as $role)
                    @if (!$user->hasRole($role->name))
                        <option value="{{ $role->name }}">{{ $role->display_name }}</option>
                    @endif
                @endforeach
            </select>
        @endif
        @if (!$addingRole)
            <a href="#" wire:click="addRole(true)"
                class="border border-blue-100 hover:bg-blue-100 inline-flex items-center text-sm rounded mt-2 mr-1 overflow-hidden rounded transition duration-100 ease-in-out">
                <span class="mx-1 leading-relaxed truncate max-w-xs px-2 py-1"><i class="fa fa-plus mx-auto"></i></span>
            </a>
        @endif
    @endcan
</div>
