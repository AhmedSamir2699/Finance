    <header class="flex items-center justify-between px-6 py-4 bg-primary-900">
        <div class="flex items-center">
            <a id="mobile-sidebar-toggle" class="lg:hidden fixed text-gray-400 hover:text-secondary-900 mx-5 z-30 top-5 right-0">
                <i class="fas fa-bars text-lg" id="toggle-icon"></i>
            </a>
        </div>
        @if (auth()->user())

            <div class="flex items-center">

                @if (session()->has('impersonator_id'))
                    <a href="{{ route('users.stop-impersonate') }}"
                        class="text-secondary-base hover:text-secondary-900 mx-5">
                        <i class="fas fa-user-alt-slash"></i>
                    </a>
                @endif

                <livewire:common.messages-icon wire:poll />

                <livewire:common.notifications-icon wire:poll />

                @if (auth()->user()->can('manage.settings'))
                    <a href="{{ route('manage.index') }}" 
                        class="text-white hover:text-gray-300 transition-colors duration-200 mx-4"
                        title="{{ __('Settings Hub') }}">
                        <i class="fas fa-cog text-lg"></i>
                    </a>
                @endif

                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = ! dropdownOpen"
                        class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none mx-4">
                        @if(auth()->user()->profile_picture)
                            <img class="object-cover w-full h-full"
                                src="{{ asset('storage/' . auth()->user()->profile_picture) }}"
                                alt="Your avatar">
                        @else
                            <img class="object-cover w-full h-full"
                                src="https://ui-avatars.com/api/?length=1&background=random&name={{ auth()->user()->name }}"
                                alt="Your avatar">
                        @endif
                    </button>
                    <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full"
                        style="display: none;"></div>
                    <div x-show="dropdownOpen"
                        class="absolute left-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl border border-grey-300"
                        style="display: none;">
                        <a href="{{ route('profile.show') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-base hover:text-white">
                            {{ __('users.view_profile') }}
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-base hover:text-white">
                            {{ __('users.edit_profile') }}
                        </a>
                        <a href="{{ route('tasks.index') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-base hover:text-white">
                            {{ __('header.tasks') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-base hover:text-white">
                                {{ __('header.logout') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </header>
