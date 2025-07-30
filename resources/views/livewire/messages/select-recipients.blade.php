<div class="flex flex-col gap-2">
    <div class="flex flex-col gap-5 lg:flex-row my-2">
        <div class="flex flex-1">
            <select wire:model.live="selectedDepartments"
                class="w-full text-center px-3 py-2 text-sm text-gray-600 bg-white border border-gray-500 rounded-md shadow-sm focus:outline-none focus:border-primary-base focus:ring focus:ring-primary-base focus:ring-opacity-50">
                <option value="*">{{ __('messages.select.all_departments') }}</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}"
                        {{ $department->id == auth()->user()->department->id ? 'selected' : '' }}>
                        {{ $department->name }}</option>
                @endforeach
            </select>
        </div>



        <div class="flex items-center flex-1">

            <label
                class="flex flex-row p-2 flex-0 items-center space-x-1 rounded-r-md border-t border-r border-b border-gray-500 bg-gray-100 hover:bg-gray-200">
                <button type="button" wire:click="selectAll" class="text-gray-700 px-2">{{ __('messages.select_all') }}
                    ({{ $users->count() }})</button>

            </label>


            <input type="text" placeholder="{{ __('messages.search') }}" wire:model.live="searchUsers"
                class="px-4  flex-1 py-2 border border-gray-300 focus:outline-none rounded-l-md border border-gray-500 focus:border-primary-base focus:ring-0" />
        </div>

    </div>

    <div class="w-full">
        @if ($searchUsers)
            <div class="flex flex-row flex-wrap gap-1">
                @foreach ($users as $user)
                    <label
                        class="flex flex-row p-2 items-center space-x-1 rounded-md border border-gray-500 bg-gray-100 cursor-pointer hover:bg-gray-200"
                        wire:click="addRecipient({{ $user['id'] }})"
                        >
                        <span class="text-gray-700 px-2">{{ $user['name'] }}</span>
                        <i class="fas fa-plus text-primary-base"></i>
                    </label>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex flex-row flex-wrap gap-2">
        @if ($selectedUsers)
            @foreach ($selectedUsers as $user)
                <span class="px-2 py-1 text-sm text-white bg-primary-base rounded-full flex flex-row">
                    <span>{{ $user['name'] }}</span>
                    <button type="button" wire:click="removeRecipient({{ $user['id'] }})"
                        class="flex text-center items-center justify-center px-1">
                        <i class="fas fa-times m-1"></i>
                    </button>
                </span>
            @endforeach
        @endif
    </div>

    <input type="hidden" name="recipients" wire:model="recipients" value="{{ $recipients }}" />

</div>
