<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#4F46E5">

        <!-- iOS support -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="min-h-screen flex flex-col w-full">
            @if (!request()->routeIs('login'))
                {{-- fixed header (Livewire) --}}
                <livewire:layout.header />
            @endif

            {{-- main content, padded to avoid fixed header --}}
            <main class="flex-1 w-full {{ !request()->routeIs('login') ? 'pt-16 pb-6' : '' }}">
                <div class="w-full mx-auto px-4">
                    {{ $slot }}
                </div>
            </main>

            @if (!request()->routeIs('login'))
                {{-- No footer for now --}}
            @endif
        </div>

        @livewireScripts
    </body>
</html>
