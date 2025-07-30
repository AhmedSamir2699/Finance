<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />


    <div class="mt-4 container mx-auto">
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
                    <div class="w-full h-full bg-white">
                        <form x-data="{ hasContent: false, hasRecipients: {{ count($recipients ?? []) }} > 0 || false }" {{-- @content-updated.window="hasContent = $event.detail.content.length > 0" --}}
                            @content-updated.window="hasContent = $event.detail.content.length > 0"
                            @recipients-updated.window="hasRecipients = $event.detail[0].length > 0"
                            action="{{ route('messages.store') }}" method="POST" wire:submit="save"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="flex flex-col justify-between w-full mx-auto px-12 py-6">

                                @if (isset($message))
                                    <div class="flex flex-col gap-2 mt-3 mb-5">
                                        <div class="flex flex-row gap-2">
                                            <span class="text-gray-700">{{ __('messages.replying_to') }}:</span>
                                            <span class="text-primary-base">{{ $message->subject }}</span>
                                        </div>
                                    </div>
                                @endif


                                <div class="mb-5">
                                    <x-text-input name="subject" label="{{ __('messages.subject') }}" class="mt-3"
                                        value="{{ isset($message) ? 'رد على: ' . $message->subject : '' }}" required />
                                </div>

                                <livewire:messages.select-recipients :recipients="$recipients ?? null" />


                                <livewire:messages.upload-attachments />

                                <x-text-editor name="content" class="mt-3" x-model="content" />

                                <input type="hidden" name="reply_to" value="{{ $message->id ?? null }}" />


                                <div class="flex justify-end mt-3">
                                    <button x-bind:disabled="!hasContent || !hasRecipients"
                                        class="inline-flex mx-auto items-center justify-center gap-x-1 px-4 py-2 text-sm font-medium text-white bg-primary-base rounded-lg hover:bg-primary-dark focus:outline-none focus:ring focus:ring-primary-dark disabled:opacity-50 disabled:pointer-events-none">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>{{ __('messages.send') }}</span>
                                    </button>
                                </div>
                        </form>

                    </div>


                </div>
            </div>
        </div>

    </div>


</x-app-layout>

@push('js')
    <script>
        console.log($event.detail);
    </script>
@endpush
