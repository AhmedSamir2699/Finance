<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-text-input id="email" placeholder="{{__('login.email')}}" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-text-input for="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            placeholder="{{__('login.password')}}"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-white border-primary-300 dark:border-primary-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-primary-200" name="remember">
                <span class="ms-2 text-sm text-primary-600 dark:text-primary-400">{{ __('login.remember_me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-primary-800" href="{{ route('password.request') }}">
                    {{ __('login.forgot_password') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('login.login_btn') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
