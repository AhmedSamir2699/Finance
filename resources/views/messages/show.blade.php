<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 container mx-auto" x-data x-init="$nextTick(() => document.getElementById('message-{{ $currentMessage->id }}').scrollIntoView({ behavior: 'smooth' }))">
        <div class="flex flex-col">
            <div class="mb-8">
                <div class="flex flex-col ">
                    <div class="flex flex-row justify-between">
                        <div class="flex flex-row flex-1 gap-1">
                            <a href="{{ route('messages.index', ['tab' => 'inbox']) }}"
                                class="block hover:bg-white hover:rounded-t-md hover:shadow-lg text-lg font-semibold  text-primary-base px-5 py-2 transition ease-in-out">
                                {{ __('messages.tabs.inbox') }}
                            </a>
                            <a href="{{ route('messages.index', ['tab' => 'inbox']) }}"
                                class="block hover:bg-white hover:rounded-t-md hover:shadow-lg text-lg font-semibold text-primary-800 px-5 py-2 transition ease-in-out">
                                {{ __('messages.tabs.sent') }}
                            </a>
                        </div>
                        <div class="flex-0">
                            <a href="{{ route('messages.create') }}"
                                class="block text-lg font-semibold bg-primary-base rounded-t-md shadow-lg text-white hover:text-secondary-300 px-5 py-2 transition ease-in-out">
                                {{ __('messages.tabs.compose') }}
                            </a>
                        </div>
                    </div>
                    <div class="w-full h-full bg-white rounded-b-lg p-3">
                        <div class="flex flex-col">

                            @foreach ($messages as $message)
                                <div id="message-{{ $message->id }}" x-data="{ expand: {{ $message->id == $currentMessage->id ? 'true' : 'false' }} }"
                                    class="flex flex-col w-full rounded-lg border border-1 mb-2">
                                    <div @click="expand = !expand"
                                        class="flex flex-row justify-between bg-gray-500 text-white px-4 py-2 rounded-t-md">

                                        <span>{{ $message->from->name }}: {{ $message->subject }}</span>
                                        <span>{{ $message->created_at->diffForHumans() }} -
                                            {{ $message->created_at }}</span>

                                    </div>
                                    <div x-show="expand" x-data="{ showRecipients: false }"
                                        class="flex flex-col p-2 pb-5 text-start transition ease-in-out duration-500 rounded-b-md shadow-lg bg-white border border-1 border-gray-500 text-center">

                                        <div class="lg:grid lg:grid-cols-2 gap-2 border-b pb-5">
                                            <div class="my-2 md:m-none">
                                                <span class="font-bold">{{ __('messages.subject') }}:</span>
                                                @if ($message->reply_to !== null)
                                                    <a href="{{ route('messages.show', $message->reply_to) }}">
                                                        <span>{{ $message->subject }}</span>
                                                    </a>
                                                @else
                                                    <span>{{ $message->subject }}</span>
                                                @endif
                                            </div>
                                            <div class="my-2 md:m-none">
                                                <span class="font-bold">{{ __('messages.sent_at') }}:</span>
                                                <span>{{ $message->created_at }}</span>
                                            </div>
                                            <div class="my-2 md:m-none">
                                                <span class="font-bold">{{ __('messages.from') }}:</span>
                                                <span>{{ $message->from->name }}</span>
                                            </div>
                                            <div class="my-2 md:m-none">
                                                <span class="font-bold">{{ __('messages.attachments.label') }}:</span>
                                                <span>
                                                    @if ($message->attachedFiles->count())
                                                        <span>{{ $message->attachedFiles->count() }} - <a
                                                                href="{{ route('messages.download-all-attachments', $message) }}">
                                                                {{ __('messages.attachments.download_all') }}
                                                                <i class="fas fa-download"></i>
                                                            </a></span>
                                                    @else
                                                        <span>0</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="my-2 md:m-none">
                                                <span
                                                    class="font-bold me-5">{{ trans_choice('messages.recipients', $message->recipients->count()) }}:</span>

                                                @if ($message->recipients->count() > 1)
                                                    <span
                                                        @click.stop="showRecipients = !showRecipients">{{ $message->recipients->count() }}
                                                        <i
                                                            :class="showRecipients == false ? 'fas fa-chevron-left' :
                                                                'fas fa-chevron-down'"></i></span>
                                                @else
                                                    <a href="{{ route('users.show', $message->recipients->first()) }}"
                                                        class="text-sm py-1 px-2 border border-1 border-gray-500 rounded-lg font-bold bg-primary-100 text-white">
                                                        {{ $message->recipients->first()->name }}
                                                    </a>
                                                @endif

                                            </div>

                                            <div class="flex flex-wrap flex-row justify-between px-5">
                                                <div class="flex flex-row gap-2">
                                                    <a href="{{ route('messages.reply', [$message]) }}"
                                                        class="text-white bg-primary-base px-4 py-2 rounded-lg hover:bg-primary-dark">
                                                        {{ __('messages.reply') }}
                                                        <i class="fas fa-reply"></i>
                                                    </a>
                                                </div>
                                            </div>



                                            @if ($message->recipients->count() > 1)
                                                <div x-show="showRecipients" class="flex flex-row flex-wrap gap-1 my-2">
                                                    @foreach ($message->recipients as $recipient)
                                                        <a href="{{ route('users.show', $recipient) }}"
                                                            class="text-sm py-1 px-2 border border-1 border-gray-500 rounded-lg font-bold bg-primary-100 text-white hover:bg-primary-300 transition-all">
                                                            {{ $recipient->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col" x-data="{ expandAttachments: false }">
                                            <div class="px-5 py-3 prose">
                                                {!! $message->content !!}
                                            </div>

                                            @if ($message->attachedFiles->count())
                                                <hr />
                                                <button class="flex flex-row items-center px-5 py-3"
                                                    @click="expandAttachments = !expandAttachments">
                                                    <span>{{ __('messages.attachments.label') }}</span>
                                                    <i x-show="!expandAttachments" class="fas fa-chevron-left"></i>
                                                    <i x-show="expandAttachments" class="fas fa-chevron-down"></i>
                                                </button>
                                                <div class="flex flex-row flex-wrap" x-show="expandAttachments">
                                                    @foreach ($message->attachedFiles as $attachment)
                                                        <div class="flex flex-row justify-between px-5 py-3">
                                                            <a href="{{ route('messages.download-attachment', $attachment) }}"
                                                                class="text-primary-base hover:text-primary-dark">
                                                                <i class="fas fa-download"></i>
                                                                <span>{{ $attachment->filename }}</span>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


</x-app-layout>
