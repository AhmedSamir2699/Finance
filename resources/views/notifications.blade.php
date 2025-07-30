
<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div class="mt-6 text-gray-500">
                    @if ($notifications->count() > 0)
                        <div class="flex flex-col mb-4">
                            <form method="POST" action="{{ route('notifications.markAllRead') }}" class="self-end mb-2">
                                @csrf
                                <button type="submit"
                                    @if(auth()->user()->notifications()->unseen()->count() === 0)
                                     disabled
                                        class="bg-slate-300 text-gray-400 cursor-not-allowed font-bold py-2 px-4 rounded "
                                    @else
                                        class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"

                                    @endif>
                                    {{ __('notification.mark_all_as_read') }}
                                </button>
                            </form>
                            @foreach ($notifications as $notification)
                            <a href="{{  route('notifications.show', $notification->id) }}"
                                class="flex justify-center px-4 py-4 -mx-2 text-gray-600 hover:text-white hover:bg-primary-base border-b border-gray-300 text-right
                                @if (!$notification->is_seen) bg-primary-100 text-white @endif"
                                >
                                <p class="flex-1 block mx-0 text-sm text-right">
                                    <span class="font-bold" href="#">{{ $notification->content }}</span>
                                </p>
                                <span class="text-xs ml-auto hover:text-white">{{ $notification->created_at->diffForHumans() }}</span>
                            </a>
                            @endforeach
                        </div>

                        <div class="my-5 px-24">
                                {{$notifications->links()}}
                        </div>
                    @else
                        <div class="text-center py-24">
                            <div class="mt-6 text-gray-500">
                                {{ __('notification.no_notifications') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
