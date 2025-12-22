<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
            <img
                src="{{ asset('images/pitstop-dark-logo.png') }}"
                alt="Pit Stop"
                class="absolute top-4 left-4 h-12 md:h-16 w-auto"
            />
        <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
            <div class="w-full sm:max-w-md rounded-2xl bg-white px-8 py-8 shadow-lg ring-1 ring-black/5">
                <img
                    src="{{ asset('images/MWAG-logo.jpeg') }}"
                    alt="MWAG Logo"
                    class="mx-auto h-20 md:h-28 lg:h-32 w-auto mb-4 rounded-lg"
                />
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
