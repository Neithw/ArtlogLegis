<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Plenário digital
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Área do parlamentar
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert>
                    {{ session('success') }}
                </x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error">
                    {{ session('error') }}
                </x-ui::alert>
            @endif

            @if ($errors->any())
                <x-ui::alert type="error">
                    {{ $errors->first() }}
                </x-ui::alert>
            @endif

            <div class="space-y-5">
                @include('plenario.partials._sessao')
                @include('plenario.partials._presenca')
                @include('plenario.partials._votacao')
                @include('plenario.partials._resultados')
            </div>
        </div>
    </div>
</x-app-layout>
