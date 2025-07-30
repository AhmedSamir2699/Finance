<div>
    @foreach ($categories as $key => $category)
        <div class="mb-4" x-data="{ isExpanded: @entangle('categories.' . $key . '.isExpanded') }">
            <div class="flex items-center justify-between cursor-pointer border py-1 px-3 bg-gray-100" 
                 @click="$wire.toggleCategory('{{ $key }}')">
                <h2 class="text-lg font-bold">{{ $category['name'] }}</h2>
            </div>

            <!-- Expandable permissions list -->
            <div 
                 x-show="isExpanded" 
                 x-transition 
                 class="flex flex-row flex-wrap gap-4 mt-2 items-center"
                 style="display: none;">
                @foreach ($category['permissions'] as $permission)
                    <div class="bg-white border shadow-md rounded px-8 py-2">
                        <div class="flex flex-row gap-3">

                            <input
                            class="shadow appearance-none border rounded text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="permissions[{{ $permission->id }}]" 
                            name="permissions[{{ $permission->id }}]"
                            type="checkbox" 
                            wire:click="togglePermission({{ $permission->id }})"
                            @cannot('role.assign-permission') disabled @endcannot
                            @if ($role->permissions->contains($permission)) checked @endif>

                            <label class="block text-gray-700 text-sm font-bold" 
                                   for="permissions[{{ $permission->id }}]">
                                {{ $permission->display_name }}
                            </label>
         
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
