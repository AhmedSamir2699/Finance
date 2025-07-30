<div class="my-5 flex flex-col" x-data="{ expandAttachments: false }">
    <div class="flex justify-start gap-2 items-center">
        <button type="button" @click="expandAttachments = !expandAttachments"
            class="text-primary-base hover:text-primary-dark focus:outline-none focus:ring focus:ring-primary-dark">
            {{ __('messages.attachments.label') }}
            @if(count($uploadedAttachments))
                <span class="text-sm text-gray-500 mx-auto">({{ count($uploadedAttachments) }})</span>
            @endif
            <i x-show="!expandAttachments" class="fas fa-chevron-left"></i>
            <i x-show="expandAttachments" class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div x-show="expandAttachments" x-transition:enter="transition ease-in duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="mt-5 flex-1 flex flex-row flex-wrap gap-3">
        <div class="flex flex-col gap-2 w-28 h-28">
            <div class="flex flex-row gap-2">
                <label type="button" for="attachments"
                    class="flex flex-col items-center justify-center gap-2 px-3 py-8 text-sm font-medium text-primary-base border border-dashed  border-primary-base rounded-lg hover:bg-primary-base hover:text-white focus:outline-none focus:ring-0 transition-all duration-300">
                    <i class="fas fa-plus fa-lg mb-3"></i>
                    <span class="text-md">{{ __('messages.attach_files') }}</span>
                </label>
                <input type="file" name="attachments[]" id="attachments" multiple class="hidden"
                    wire:model.defer="attachments" />
            </div>
        </div>

        @foreach ($uploadedAttachments as $key => $attachment)

            <div class="flex flex-col gap-2 items-center justify-between bg-gray-100 px-2 py-4 rounded-lg relative w-28 h-28">
                <button type="button" wire:click="removeAttachment('{{ $key }}')"
                class="text-red-500 hover:text-red-700 focus:outline-none focus:ring focus:ring-red-500
                absolute top-0 right-0 px-2 py-1 rounded-full bg-white bg-opacity-50 shadow-md hover:shadow-lg transition ease-in-out duration-300
                ">
                <i class="fas fa-trash"></i>
            </button>
                <i class="fas fa-{{ $attachment['icon'] }} text-primary-base fa-xl mt-4"></i>
                <span class="text-sm truncate w-20" title="{{$attachment['filename']}}">{{ $attachment['filename'] }}</span>
            </div>
            
        @endforeach
    </div>


</div>
