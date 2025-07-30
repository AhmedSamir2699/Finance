<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-8">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <div class="bg-white px-4 py-3 flex items-center justify-between sm:px-6">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                {{__('requests.index.title')}}    
                            </h3>   
                        </div>
                        {{-- All previous Requests Table --}}
                        <div class="flex items-center">
                            <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:border-primary-700 focus:shadow-outline-primary active:bg-primary-700 transition ease-in-out duration-150">
                                {{__('requests.index.new_request')}}
                            </a>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{__('requests.show.id')}}</th>
                                <th class="px-6 py-3 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{__('requests.show.title')}}</th>
                                <th class="px-6 py-3 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{__('requests.show.status')}}</th>
                                <th class="px-6 py-3 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{__('requests.show.created_at')}}</th>
                                <th class="px-6 py-3 bg-gray-50 text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">{{__('requests.show.updated_at')}}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($requests as $request)
                                <tr @click="window.location='{{ route('requests.show', $request) }}'" class="cursor-pointer hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">{{ $request->id }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-bold text-gray-900">{{ $request->requestForm->title }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-bold
                                    @if ($request->status == 'in_progress')
                                        text-black
                                    @elseif ($request->status == 'approved')
                                        text-green-500
                                    @elseif ($request->status == 'rejected')
                                        text-red-500
                                    @elseif ($request->status == 'needs_attention')
                                        text-yellow-500
                                    @endif
                                    ">{{ __('requests.status.'.$request->status) }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{ $request->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{ $request->updated_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{$requests->links()}}
                </div>

</x-app-layout>
