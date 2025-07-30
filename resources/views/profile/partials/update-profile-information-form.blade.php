<section>
    <header>

    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
        @csrf
        <div>
            <label class="block text-gray-700 mb-2">{{ __('users.profile_picture') }}</label>
            <div x-data="{ imageUrl: '{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : '' }}' }" class="flex flex-col items-start">
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden" @change="
                    const file = $event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => imageUrl = e.target.result;
                        reader.readAsDataURL(file);
                    }
                ">
                <label for="profile_picture" class="cursor-pointer flex flex-col items-center justify-center w-64 h-64 border-2 border-dashed border-gray-300 rounded bg-gray-50 hover:bg-gray-100">
                    <template x-if="imageUrl">
                        <img :src="imageUrl" alt="Profile Preview" class="w-64 h-64 object-cover rounded">
                    </template>
                    <template x-if="!imageUrl">
                        <span class="text-gray-400">{{ __('users.drag_drop_image') }}</span>
                    </template>
                </label>
                @error('profile_picture')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label class="block text-gray-700" for="name">{{ __('users.fullname') }}</label>
            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
        </div>
        <div>
            <label class="block text-gray-700" for="email">{{ __('users.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
        </div>
        <div>
            <label class="block text-gray-700" for="phone">{{ __('users.phone') }}</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50" required>
        </div>
        <div>
            <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded hover:bg-primary-600">{{ __('users.edit.save') }}</button>
        </div>
    </form>
</section>
