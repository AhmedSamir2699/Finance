<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div class="w-[90%] mx-auto">
        <div class="flex flex-col my-5">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">
                {{ __('users.summary.general.title') }}
            </h3>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('users.summary.general.select_department') }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($departments as $department)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <a href="{{ route('users.summary.general.department', $department->id) }}" 
                               class="block">
                                <h5 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $department->name }}
                                </h5>
                                <p class="text-sm text-gray-600">
                                    {{ __('users.summary.general.users_count', ['count' => $department->users->count()]) }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 