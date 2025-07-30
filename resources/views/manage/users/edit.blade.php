<x-app-layout>
    <div class="container px-6 py-8 mx-auto">
        <h2 class="text-xl font-semibold text-gray-700 leading-tight">{{ __('users.edit.headline') }}</h2>

        <div class="mt-4">
            <div class="p-6 bg-white rounded-md shadow-md">
                <livewire:users.roles-editor :user="$user" />

                <h4 class="text-lg font-semibold text-gray-700 leading-tight mt-6">{{ __('users.edit.user_information') }}</h4>
                <form action="{{ route('manage.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">

                        <!-- Profile Picture -->
                        <div class="sm:col-span-2">
                            <div x-data="{ imageUrl: '{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}' }" class="flex flex-col items-start">
                                <label class="block text-gray-700 mb-2">{{ __('users.profile_picture') }}</label>
                                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden" @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => imageUrl = e.target.result;
                                        reader.readAsDataURL(file);
                                    }
                                ">
                                <label for="profile_picture" class="cursor-pointer flex flex-col items-center justify-center w-48 h-48 border-2 border-dashed border-gray-300 rounded bg-gray-50 hover:bg-gray-100">
                                    <template x-if="imageUrl">
                                        <img :src="imageUrl" alt="Profile Preview" class="w-48 h-48 object-cover rounded">
                                    </template>
                                    <template x-if="!imageUrl">
                                        <span class="text-gray-400">{{ __('users.drag_drop_image') }}</span>
                                    </template>
                                </label>
                                @error('profile_picture')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <x-text-input for="fullname" label="{{ __('users.fullname') }}" name="name"
                            value="{{ $user->name }}" required />

                        <x-text-input for="email" label="{{ __('users.email') }}" name="email"
                            value="{{ $user->email }}" required />

                        <x-text-input for="phone" label="{{ __('users.phone') }}" name="phone"
                            value="{{ $user->phone }}" required />

                        <x-text-input for="position" label="{{ __('users.position') }}" name="position"
                            value="{{ $user->position }}" required />

                        <!-- Work Information -->
                        <div>
                            <label class="text-gray-700" for="shift_start">{{ __('users.shift_start') }}</label>
                            <input type="time" name="shift_start" id="shift_start" 
                                value="{{ old('shift_start', $user->shift_start) }}" 
                                class="form-input w-full mt-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="text-gray-700" for="shift_end">{{ __('users.shift_end') }}</label>
                            <input type="time" name="shift_end" id="shift_end" 
                                value="{{ old('shift_end', $user->shift_end) }}" 
                                class="form-input w-full mt-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="text-gray-700" for="work_location">{{ __('users.work_location') }}</label>
                            <input type="text" name="work_location" id="work_location" 
                                value="{{ old('work_location', $user->work_location) }}" 
                                class="form-input w-full mt-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-600 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="text-gray-700" for="is_remote_worker">{{ __('users.is_remote_worker') }}</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_remote_worker" id="is_remote_worker" value="1" 
                                        {{ old('is_remote_worker', $user->is_remote_worker) ? 'checked' : '' }}
                                        class="form-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ms-2 text-gray-700">{{ __('users.remote_worker') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Admin-specific fields -->
                        <div>
                            <label class="text-gray-700" for="department">{{ __('users.department') }} <small
                                    class="text-red-500">*</small></label>
                            <select class="form-select w-full mt-2 rounded-md focus:border-indigo-600" name="department_id">
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-gray-700" for="is_active">{{ __('users.status') }} <small
                                    class="text-red-500">*</small></label>
                            <select class="form-select w-full mt-2 rounded-md focus:border-indigo-600" name="is_active">
                                <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>
                                    {{ __('users.active') }}</option>
                                <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>
                                    {{ __('users.inactive') }}</option>
                            </select>
                        </div>

                        <div class="block w-full h-full text-center justify-end mt-4 col-span-2">
                            <button class="px-4 py-2 mx-auto bg-green-800 text-white rounded-md hover:bg-green-700 focus:outline-none focus:bg-green-700">{{__('users.edit.save')}}</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
