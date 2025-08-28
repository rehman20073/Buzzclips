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
    <body class="font-sans antialiased bg-gray-950">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
            <div class="absolute inset-0 opacity-30 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full blur-3xl bg-fuchsia-500"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full blur-3xl bg-sky-500"></div>
            </div>

            <a href="/" class="relative z-10 inline-flex items-center space-x-2">
                <span class="inline-flex h-9 w-9 rounded-md bg-gradient-to-br from-fuchsia-500 via-violet-500 to-sky-500"></span>
                <span class="text-white text-lg font-semibold">BuzzClips</span>
            </a>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-6 bg-white/5 backdrop-blur-md border border-white/10 shadow-lg overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
            <p class="relative z-10 mt-6 text-white/50 text-xs">Welcome back — let’s clip it.</p>
        </div>
    </body>
</html>
