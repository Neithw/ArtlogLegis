<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Dados institucionais
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
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

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center
                                   rounded-lg bg-slate-100 text-slate-500
                                   dark:bg-neutral-800 dark:text-neutral-400">
                            <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                Informações da instituição
                            </h3>

                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Consulte os dados de identificação institucional.
                            </p>
                        </div>
                    </div>

                    @can('update', $camara)
                        <x-ui::button :href="route('camaras.edit', $camara)">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            Editar dados
                        </x-ui::button>
                    @endcan
                </header>

                <dl class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Nome institucional
                        </dt>

                        <dd class="mt-1 font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $camara->nome }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            CNPJ
                        </dt>

                        <dd class="mt-1 text-slate-700 dark:text-neutral-300">
                            {{ $camara->cnpj_formatado ?? 'Não informado' }}
                        </dd>
                    </div>
                </dl>
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
