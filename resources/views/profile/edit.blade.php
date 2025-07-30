<x-app-layout>
    
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="mb-4 text-yellow-700 bg-yellow-100 border-r-2 border-yellow-400 p-3 rounded">
                        {{ __('users.tab_save_disclaimer') }}
                    </div>
                    <div x-data="{ tab: 'profile' }">
                        <div class="mb-4 border-b border-gray-200">
                            <nav class="flex space-x-4 rtl:space-x-reverse" aria-label="Tabs">
                                <button type="button" @click="tab = 'profile'" :class="tab === 'profile' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">{{ __('users.profile_info') }}</button>
                                <button type="button" @click="tab = 'work'" :class="tab === 'work' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">{{ __('users.work_info') }}</button>
                                <button type="button" @click="tab = 'password'" :class="tab === 'password' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">{{ __('users.password') }}</button>
                            </nav>
                        </div>
                        <div x-show="tab === 'profile'">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        <div x-show="tab === 'work'">
                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 mt-4">
                                @csrf
                                <div>
                                    <label class="block text-gray-700" for="position">{{ __('users.position') }}</label>
                                    <input type="text" name="position" id="position" value="{{ auth()->user()->position }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" readonly>
                                </div>
                                <div>
                                    <label class="block text-gray-700">{{ __('users.roles') }}</label>
                                    <input type="text" value="{{ auth()->user()->roles()->pluck('display_name')->join(', ') }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed" readonly>
                                </div>
                                <div>
                                    <label class="block text-gray-700" for="shift_start">{{ __('users.shift_start') }}</label>
                                    <input type="time" name="shift_start" id="shift_start" value="{{ old('shift_start', auth()->user()->shift_start) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-gray-700" for="shift_end">{{ __('users.shift_end') }}</label>
                                    <input type="time" name="shift_end" id="shift_end" value="{{ old('shift_end', auth()->user()->shift_end) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label class="block text-gray-700" for="work_location">{{ __('users.work_location') }}</label>
                                    <input type="text" name="work_location" id="work_location" value="{{ old('work_location', auth()->user()->work_location) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_remote_worker" id="is_remote_worker" value="1" {{ old('is_remote_worker', auth()->user()->is_remote_worker) ? 'checked' : '' }} readonly disabled class="form-checkbox h-5 w-5 text-primary-600 me-5">
                                    <label for="is_remote_worker" class="ml-2 block text-gray-700">{{ __('users.is_remote_worker') }}</label>
                                </div>
                                <div>
                                    <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">{{ __('users.edit.save') }}</button>
                                </div>
                            </form>
                        </div>
                        <div x-show="tab === 'password'">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
