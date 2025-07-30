<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('users.executive-plan.migrate.store', [$user]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex flex-col items-start justify-between gap-4 mb-4">
                        <div class="flex gap-4 items-center">
                            <label for="fromUser" class="block text-sm font-medium text-gray-700 mr-2">
                                {{ __('executive-plan.migrate.from_user') }}
                            </label>
                            <input type="text" name="fromUser" id="fromUser" value="{{ $user->name }}"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                readonly disabled>
                        </div>
                        <div class="flex gap-4 items-center">
                            <label for="toUser" class="block text-sm font-medium text-gray-700 mr-2">
                                {{ __('executive-plan.migrate.to_user') }}
                            </label>
                            <select name="toUser" id="toUser"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                @foreach ($users as $toUser)
                                    @if ($toUser->id == $user->id)
                                        @continue
                                    @endif
                                    <option value="{{ $toUser->id }}">{{ $toUser->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-4 items-center">
                            <label for="year" class="block text-sm font-medium text-gray-700 mr-2">
                                {{ __('executive-plan.migrate.year') }}
                            </label>
                            <input type="number" name="year" id="year" value="{{ $selectedYear }}"
                                readonly
                                class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                min="2000" max="2100" step="1" required>

                            <label for="month" class="block text-sm font-medium text-gray-700 mr-2">
                                {{ __('executive-plan.migrate.month') }}
                            </label>
                            <select name="month" id="month"
                                readonly
                                class="border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                @for ($month = 1; $month <= 12; $month++)
                                    <option @if ($selectedMonth == $month) selected @else disabled @endif
                                        value="{{ $month }}">
                                        {{ Carbon\Carbon::create(date('Y'), $month)->translatedFormat('F') }}</option>
                                @endfor
                            </select>
                        </div>

                        <p class="block text-sm font-medium text-red-700 mr-2">
                            {{ __('executive-plan.migrate.disclaimer') }}
                        </p>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('executive-plan.migrate.submit') }}
                        </button>
                    </div>
            </div>
        </div>

</x-app-layout>
