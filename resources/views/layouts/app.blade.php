<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="bg-gray-100 font-sans antialiased">
        <div class="min-h-screen flex flex-col max-w-md mx-auto relative">
            @if (!request()->routeIs('login'))
                {{-- fixed header (Livewire) --}}
                <livewire:layout.header />
            @endif

            {{-- main content, padded to avoid fixed tabs --}}
            <main class="flex-1 w-full {{ !request()->routeIs('login') ? 'pb-[96px]' : '' }}">
                <div class="{{ !request()->routeIs('login') ? 'px-4 pt-2' : '' }}">
                    {{ $slot }}
                </div>
            </main>

            @if (!request()->routeIs('login'))
                {{-- fixed bottom tabs (Livewire) --}}
                <livewire:layout.tabs />
            @endif
        </div>

        @livewireScripts
    </body>
</html>
