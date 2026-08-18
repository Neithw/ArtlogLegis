@php
    $tipo = \App\Models\Sessao::TIPOS[$sessao->tipo] ?? ucfirst(str_replace('_', ' ', $sessao->tipo));

    $situacao = \App\Models\Sessao::SITUACOES[$sessao->situacao] ?? ucfirst(str_replace('_', ' ', $sessao->situacao));

    $variante = match ($sessao->situacao) {
        'aberta' => 'success',
        'convocada', 'suspensa' => 'info',
        default => 'neutral',
    };

    $usuarioIsRoot = auth()->user()->isRoot();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Atividade legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes da sessão
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
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

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">

                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                   bg-slate-100 text-slate-500
                                   dark:bg-neutral-800 dark:text-neutral-400">
                            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                    @if ($sessao->numero !== null && $sessao->ano !== null)
                                        {{ $sessao->numero }}ª Sessão {{ $tipo }}
                                    @else
                                        Sessão {{ $tipo }}
                                    @endif
                                </h3>

                                <x-ui::badge :variant="$variante">
                                    {{ $situacao }}
                                </x-ui::badge>
                            </div>

                            <p class="mt-2 text-sm text-slate-500 dark:text-neutral-400">
                                @if ($sessao->numero !== null && $sessao->ano !== null)
                                    Sessão nº {{ $sessao->numero }} do ano de {{ $sessao->ano }}.
                                @else
                                    Sessão em preparação, aguardando convocação e numeração.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        @can('update', $sessao)
                            <x-ui::button :href="route('sessoes.edit', $sessao)" variant="secondary">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                Editar
                            </x-ui::button>
                        @endcan

                        <x-ui::button :href="route('sessoes.index')" variant="secondary">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Voltar
                        </x-ui::button>
                    </div>
                </div>

                <dl
                    class="grid gap-px border-t border-slate-200 bg-slate-200
                           sm:grid-cols-2 lg:grid-cols-4
                           dark:border-neutral-800 dark:bg-neutral-800">

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                            Data prevista
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $sessao->data_hora_inicio_previsto->format('d/m/Y') }}
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                            Horário
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $sessao->data_hora_inicio_previsto->format('H:i') }}
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                            Local
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $sessao->local ?: 'Local a definir' }}
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt
                            class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                            Legislatura
                        </dt>

                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $sessao->legislatura->numero }}ª Legislatura
                        </dd>
                    </div>
                </dl>
            </section>

            <x-ui::card>
                <header
                    class="border-b border-slate-200 px-4 py-5 sm:px-6
                           dark:border-neutral-800">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                        Informações da sessão
                    </h3>

                    <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                        Dados institucionais e informações de registro.
                    </p>
                </header>

                <dl class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                    @if ($usuarioIsRoot)
                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Câmara
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $sessao->camara->nome }}
                            </dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Tipo da sessão
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $tipo }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Período da legislatura
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $sessao->legislatura->data_inicio->format('d/m/Y') }}
                            a
                            {{ $sessao->legislatura->data_fim->format('d/m/Y') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Cadastrada por
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $sessao->criadoPor->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Data do cadastro
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $sessao->created_at->format('d/m/Y \à\s H:i') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                            Última atualização
                        </dt>

                        <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $sessao->updated_at->format('d/m/Y \à\s H:i') }}
                        </dd>
                    </div>
                </dl>
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
