<!doctype html>
<html lang="{{ \App\Helpers\SettingsHelper::language() }}" dir="{{ \App\Helpers\SettingsHelper::language() == 'ar' ? 'rtl' : 'ltr' }}" class="{{ \App\Helpers\SettingsHelper::language() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{asset('storage/'.\App\Helpers\SettingsHelper::get('app_favicon', '/favicon.ico'))}}">
    <meta name="msapplication-TileColor" content="#035944">
    <meta name="theme-color" content="#035944">
    <title>{{ \App\Helpers\SettingsHelper::appName() }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <div>
        <div class="flex h-screen bg-gray-200 font-roboto">
            @include('layouts.sidebar')

            <div class="flex flex-col flex-1 overflow-hidden lg:mr-64">
                @include('layouts.header')


                <main class="flex-1 md:overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="mx-auto px-6 py-8">

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>
    @livewireScripts
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        Livewire.hook('request', ({
            fail
        }) => {
            fail(({
                status,
                preventDefault
            }) => {
                if (status === 419) {
                    preventDefault()

                    window.location.reload()
                }
            })
        })
    </script>
</body>

</html>
