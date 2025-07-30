<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div class="w-[90%] mx-auto">
        <div class="flex flex-col my-5">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">
                {{ __('users.summary.general.department_users', ['department' => $department->name]) }}
            </h3>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('users.summary.general.select_user') }}
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($users as $user)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                            <a href="{{ route('users.summary.show', [
                                'user' => $user->id,
                                'start_date' => \Carbon\Carbon::now()->startOfMonth()->toDateString(),
                                'end_date' => \Carbon\Carbon::now()->endOfMonth()->toDateString(),
                            ]) }}" 
                               class="block">
                                <h5 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $user->name }}
                                </h5>
                                <p class="text-sm text-gray-600">
                                    {{ $user->position ?? __('users.no_position') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('users.summary.general.last_activity') }}: 
                                    {{ $user->updated_at ? $user->updated_at->diffForHumans() : __('users.summary.general.no_activity') }}
                                </p>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if($users->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">{{ __('users.summary.general.no_users') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 