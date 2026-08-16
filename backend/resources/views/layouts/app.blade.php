<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ArtLog Legis') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        const temaSalvo = localStorage.getItem('theme');

        const usarTemaEscuro =
            temaSalvo === 'dark' ||
            (
                temaSalvo === null &&
                window.matchMedia('(prefers-color-scheme: dark)').matches
            );

        document.documentElement.classList.toggle('dark', usarTemaEscuro);

        document.documentElement.classList.toggle(
            'sidebar-collapsed',
            localStorage.getItem('sidebar-collapsed') === 'true'
        );
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-slate-100 font-sans antialiased text-slate-900 dark:bg-neutral-950 dark:text-slate-100">
    <x-banner />

    <div x-data @keydown.escape.window="$store.layout.sidebarOpen = false"
        class="min-h-screen bg-slate-100 transition-colors dark:bg-neutral-950">
        @persist('app-navigation')
            @include('navigation-menu')
        @endpersist

        <div class="app-content min-h-screen pt-16">
            <div class="flex min-h-[calc(100vh-4rem)] flex-col">
                @if (isset($header))
                    <header
                        class="shrink-0 border-b border-slate-200 bg-white
                       dark:border-neutral-800 dark:bg-neutral-900">
                        <div class="flex h-20 items-center px-4 sm:px-6 lg:px-8">
                            <div class="w-full">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endif

                <main class="flex-1">
                    {{ $slot }}
                </main>

                <footer
                    class="shrink-0 border-t border-slate-200 bg-white
                   dark:border-neutral-800 dark:bg-neutral-900">
                    <div
                        class="px-4 py-3 text-center text-xs
                       text-slate-500 dark:text-neutral-500">
                        © {{ now()->year }} ArtLog. Todos os direitos reservados.
                    </div>
                </footer>
            </div>
        </div>
    </div>

    @stack('modals')

    @livewireScriptConfig
</body>

</html>
