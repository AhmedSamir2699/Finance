<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container mt-4 mx-auto">
        <div class="flex flex-col bg-white py-2 px-4 rounded-md shadow-md border">
            <div class="flex-1">
                <div class="grid grid-cols-2">

                    <h1 class="text-2xl font-semibold text-gray-700 inline-block"> {{ $cell->column->name }}</h1>
                    <div class="w-full flex gap-2 mt-2 justify-center">
                        <div class="">
                            <a href="{{ route('executive-plan.cell.edit', [$cell->id]) }}"
                                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                                {{ __('executive-plan.cell.edit.title') }} <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex gap-2 mt-5">
                        <div class="flex-1">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ Carbon\Carbon::parse($cell->date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="flex-0">
                            <h3 class="font-semibold text-gray-700 inline-block">
                                {{ $cell->value }}</h3>
                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-4 flex-1 pt-4 pb-4">
                <div class="flex gap-2">
                    <div class="flex-1">
                        <p class="py-2 px-3 prose">{!! $cell->description !!}</p>
                    </div>
                </div>
            </div>

        </div>


    </div>
</x-app-layout>

