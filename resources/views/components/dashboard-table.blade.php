@props(['title'])

<div class="bg-white rounded-lg shadow-md">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-center">
            @if(isset($header))
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ $header }}
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div> 