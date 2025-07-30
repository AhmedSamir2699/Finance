<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-5 w-3/4 mx-auto bg-white py-3 px-5 rounded-lg shadow">
        <div class="flex flex-row justify-between mb-4">
            <h1 class="text-xl font-semibold text-gray-900">
                {{ __('executive-plan.title') }}
                <select name="year" id="year"
                    class="px-8 mx-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center">
                    @foreach ($availableYears as $year)
                        <option>
                            <a href="{{ route('executive-plan.index', ['year' => $year]) }}">{{ $year }}</a>
                        </option>
                    @endforeach
                </select>
            </h1>
            @if (auth()->user()->can('executive-plan.export-any') ||
                    auth()->user()->can('executive-plan.export-department') ||
                    (auth()->id() == $user->id && auth()->user()->can('executive-plan.export-self')))
                <a href="{{ route('executive-plan.export', ['year' => $currentYear, 'user' => $user]) }}"
                    class="inline text-md hover:text-primary-900 text-primary-base font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    <i class="fas fa-file-export"></i>
                    {{ __('executive-plan.export_to_excel') }}
                </a>
            @endif
        </div>

        <table class="min-w-full text-center  border border-gray-300">
            <thead>
                <tr>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('executive-plan.month') }}
                    </th>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('executive-plan.events') }}
                    </th>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('executive-plan.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($month = 1; $month <= 12; $month++)
                    <tr class="group text-gray-900 hover:bg-primary-50 hover:text-white transition-all ease-in-out">
                        <td class="py-2 border-r border-b border-gray-400 group-hover:border-gray-50">
                            {{ Carbon\Carbon::create($currentYear, $month)->translatedFormat('F') }}
                            {{-- - {{ $hijri::Date('Y/F/d', Carbon\Carbon::create($currentYear, $month)) }} --}}
                        </td>
                        <td class="py-2 border-r border-b border-gray-400 group-hover:border-gray-50">
                            {{ $plans->whereMonth('date', $month)->whereYear('date', $currentYear)->whereNotNull('value')->whereNot('value', '')->where('user_id', $user->id)->count() }}
                        </td>
                        <td
                            class="py-2 border-r border-b border-gray-400 group-hover:border-gray-50 flex flex-wrap justify-center items-center gap-3">

                            @if ($user->id !== auth()->id())
                                <a href="{{ route('executive-plan.show', ['year' => $currentYear, 'month' => $month, 'user' => $user]) }}"
                                    class="">
                                    {{ __('executive-plan.view') }}
                                </a>
                            @else
                                <a href="{{ route('executive-plan.show', ['year' => $currentYear, 'month' => $month]) }}"
                                    class="">
                                    {{ __('executive-plan.view') }}
                                </a>
                            @endif

                            <a href="{{ route('executive-plan.clone', ['year' => $currentYear, 'month' => $month]) }}"
                                class="">
                                {{ __('executive-plan.clone') }}
                            </a>

                            @can('executive-plan.migrate')
                                <a href="{{ route('users.executive-plan.migrate', ['user' => $user, 'year' => $currentYear, 'month' => $month]) }}"
                                    class="">
                                    {{ __('executive-plan.migrate.button') }}
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</x-app-layout>
