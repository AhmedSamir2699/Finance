<div x-data="{ addStepModal: false }">
    <button class="my-4 bg-primary-600 text-white py-1 px-4 rounded hover:text-secondary-base"
        @click="addStepModal = true">
        {{ __('manage.forms.path.add_step') }}
    </button>
    <div class="flex flex-col">
        <div class="flex justify-between items-center py-2 px-4 gap-3 border border-gray-200 bg-white shadow rounded">
            <div class="flex items-center gap-3 cursor-not-allowed mx-1 flex-0">
                <strong class="text-lg font-semibold text-gray-700 mx-3">0. {{ __('manage.forms.path.sender') }}</strong>
            </div>
        </div>
    </div>

    <div class="flex flex-col overflow-hidden"  wire:sortable="updateStepOrder">
        @foreach ($path as $step)
            <div class="flex justify-between items-center py-2 px-4 gap-3 border border-gray-200 bg-white shadow rounded my-2"
            wire:sortable.item="{{ $step->id }}" wire:key="step-{{ $step->id }}"
            >
                <div class="flex items-center gap-3 cursor-grab mx-1 flex-0 "
                wire:sortable.handle
                >
                    <strong
                        class="text-lg font-semibold text-gray-700 mx-3">{{ $step->step_order .'. '.$step->department->name.' - '. ($step->user->name ?? $step->role->display_name) }}</strong>
                </div>
                <div class="flex items-center space-x-2 gap-3 justify-between flex-0">
                    <button class="text-blue-500 hover:text-blue-700"
                        wire:click="editField({{ $step->id }})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="text-red-500 hover:text-red-700"
                        wire:click="deleteField({{ $step->id }})">
                        <i class="fas fa-trash"></i>
                    </button>

                </div>
            </div>
        @endforeach

    </div>

    <x-modal name="addStepModal">
        <form wire:submit.prevent="storeStep">
            @csrf
            <div class="bg-white px-4 py-3  sm:px-6">
                <div class="grid grid-cols-1 gap-6">
                    <div class="">
                        <label for="department"
                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.path.department') }}</label>
                        <select name="department" id="department" wire:model.live="stepform.department_id"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="" selected>{{ __('manage.forms.path.select_department') }}</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($stepform['department_id'])

                        <div class="flex flex-row justify-between gap-3">
                            <div class="">
                                <label for="role"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.forms.path.role') }}</label>
                                <select name="role" id="role" wire:model.live="stepform.role_id"
                                {{ $stepform['user_id'] ? 'disabled' : ''}}
                                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">{{ __('manage.forms.path.select_role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="">
                                <label for="user"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.forms.path.user') }}</label>
                                <select name="user" id="user" wire:model.live="stepform.user_id"
                                    {{ $stepform['role_id'] ? 'disabled' : '' }}
                                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">{{ __('manage.forms.path.select_user') }}</option>
                                    @foreach ($department_users as $user)
                                        <option value="{{ $user->id }}">{{$user->roles->first()->display_name}} - {{ $user->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>


                    @endif


                    <div class="">
                        <button type="submit"
                        {{ $saveIsDisabled ? 'disabled' : '' }}
                            class="items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('manage.forms.fields.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </x-modal>
</div>
