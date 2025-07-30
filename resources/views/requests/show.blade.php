<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col gap-4 p-5 my-5 rounded bg-white border border-primary-base">
        <div class="flex justify-start items-center">
            @if ($isApprover)
                <div class="flex mb-4 gap-4 text-center">
                    <a href="{{ route('requests.return', $request) }}"
                        class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-4 rounded">إعادة</a>
                    <a href="{{ route('requests.approve', $request) }}"
                        class="bg-green-500 hover:bg-green-700 text-white py-1 px-4 rounded">قبول</a>
                    <a href="{{ route('requests.reject', $request) }}"
                        class="bg-red-500 hover:bg-red-700 text-white py-1 px-4 rounded">رفض</a>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="grid grid-cols-1 gap-4">
                <div class="flex flex-col md:flex-row items-center">
                    <b>{{ __('requests.show.submitter.name') }}</b>
                    <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                        {{ $request->user->name }} - {{ $request->user->department->name }}
                    </span>
                </div>
                <div class="flex flex-col md:flex-row items-center">
                    <b>{{ __('requests.show.submitter.phone') }}</b>
                    <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                        {{ $request->user->phone }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <div class="flex flex-col md:flex-row items-center">
                    <b>{{ __('requests.show.form') }}</b>
                    <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                        {{ $request->requestForm->title }} - {{ $request->requestForm->category->name }}
                    </span>
                </div>
                <div class="flex flex-col md:flex-row items-center">
                    <b>{{ __('requests.show.status') }}</b>
                    <span
                        class="border-none border-b border-b-1 border-gray-300 rounded-md p-2
                                    @if ($request->status == 'in_progress') 
                                        text-black
                                    @elseif ($request->status == 'approved')
                                        text-green-500
                                    @elseif ($request->status == 'rejected')
                                        text-red-500
                                    @elseif ($request->status == 'needs_attention')
                                        text-yellow-500 
                                    @endif
                    ">
                        {{ __('requests.status.' . $request->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4 p-5 my-5 rounded bg-white border border-primary-base">

        @foreach ($request->fields['order'] as $key)
            @php
                $value = $request->fields['data'][$key];
            @endphp

            <div class="flex flex-col md:flex-row items-center">
                <b>{{ str_replace('_', ' ', $key) }}</b>

                <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                    @if (is_array($value))
                        @foreach ($value as $k => $v)
                            @if (Str::startsWith($v, 'files/'))
                                {{-- Check if it's a file path --}}
                                <a href="{{ asset($v) }}" download class="text-blue-500 flex items-center gap-2">
                                    <i class="fas fa-download"></i>
                                    {{ $k }}
                                </a>
                            @else
                                {{ $k }} - {{ $v }}<br>
                            @endif
                        @endforeach
                    @else
                        {{ str_replace('_', ' ', $value) }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>


    {{-- Steps Section --}}

    <div class="flex flex-col gap-4 p-5 my-5 rounded bg-white border border-primary-base">
        <div class="flex flex-col gap-4">
            @foreach ($steps as $step)
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="flex flex-row items-center">
                        <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                            @if ($step->approved)
                                <i class="fas fa-check text-green-500"
                                    title=" {{ \Carbon\Carbon::parse($step->approved_at)->format('d/m/Y h:i:s A') }}"></i>
                            @else
                                <i class="fas fa-hourglass-half text-gray-400"></i>
                            @endif
                        </span>
                        <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                            {{ $step->department->name }}
                        </span>
                        <span class="border-none border-b border-b-1 border-gray-300 rounded-md p-2">
                            {{ $step->user->name ?? $step->role->display_name }}
                        </span>

                    </div>

                </div>
            @endforeach
        </div>

</x-app-layout>
