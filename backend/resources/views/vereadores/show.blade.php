<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes do vereador
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-xl
                                   bg-indigo-50 text-lg text-indigo-600
                                   dark:bg-indigo-500/10 dark:text-indigo-400">
                            <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $vereador->nome }}
                            </h3>

                            <p class="mt-0.5 text-sm text-slate-500 dark:text-neutral-400">
                                {{ $vereador->nome_parlamentar ?? 'Nome parlamentar não informado' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui::button :href="route('vereadores.index')" variant="secondary">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Voltar
                        </x-ui::button>

                        @can('update', $vereador)
                            <x-ui::button :href="route('vereadores.edit', $vereador)">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                Editar
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Câmara
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $vereador->camara?->nome ?? 'Câmara indisponível' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Nome parlamentar
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $vereador->nome_parlamentar ?? 'Não informado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            E-mail institucional
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $vereador->email_institucional ?? 'Não informado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Telefone institucional
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $vereador->telefone_institucional_formatado ?? 'Não informado' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Conta vinculada
                        </p>

                        @if ($vereador->user)
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-950 dark:text-neutral-100">
                                        {{ $vereador->user->name }}
                                    </p>

                                    <p class="text-xs text-slate-500 dark:text-neutral-500">
                                        {{ $vereador->user->email }}
                                    </p>
                                </div>

                                @if ($vereador->user->trashed())
                                    <x-ui::badge variant="danger">
                                        Arquivada
                                    </x-ui::badge>
                                @elseif (!$vereador->user->ativo)
                                    <x-ui::badge variant="warning">
                                        Desativada
                                    </x-ui::badge>
                                @else
                                    <x-ui::badge variant="success">
                                        Ativa
                                    </x-ui::badge>
                                @endif
                            </div>
                        @else
                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-500">
                                Sem conta vinculada
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 pt-6 md:col-span-2 dark:border-neutral-800">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Biografia institucional
                        </p>

                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-neutral-300">
                            {{ $vereador->biografia ?? 'Não informada' }}
                        </p>
                    </div>
                </div>
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
