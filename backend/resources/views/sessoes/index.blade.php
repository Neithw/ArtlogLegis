<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Atividade legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Sessões
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert class="mb-6">
                    {{ session('success') }}
                </x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error" class="mb-6">
                    {{ session('error') }}
                </x-ui::alert>
            @endif

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Sessões cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{ $usuarioIsRoot
                                ? 'Acompanhe as sessões legislativas de todas as Câmaras.'
                                : 'Organize e acompanhe as sessões da atividade legislativa.' }}
                        </p>
                    </div>

                    @can('create', \App\Models\Sessao::class)
                        <x-ui::button :href="route('sessoes.create')">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Nova sessão
                        </x-ui::button>
                    @endcan
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Sessão</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Legislatura</th>
                            <th scope="col">Data e local</th>
                            <th scope="col">Situação</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sessoes as $sessao)
                            @php
                                $tipo =
                                    \App\Models\Sessao::TIPOS[$sessao->tipo] ??
                                    ucfirst(str_replace('_', ' ', $sessao->tipo));

                                $situacao =
                                    \App\Models\Sessao::SITUACOES[$sessao->situacao] ??
                                    ucfirst(str_replace('_', ' ', $sessao->situacao));

                                $variante = match ($sessao->situacao) {
                                    'aberta' => 'success',
                                    'convocada', 'suspensa' => 'info',
                                    default => 'neutral',
                                };
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <p class="font-semibold text-slate-950 dark:text-neutral-100">
                                        @if ($sessao->numero !== null && $sessao->ano !== null)
                                            {{ $sessao->numero }}ª Sessão {{ $tipo }}
                                        @else
                                            Sessão {{ $tipo }}
                                        @endif
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                                        @if ($sessao->ano !== null)
                                            Ano {{ $sessao->ano }}
                                        @else
                                            Aguardando convocação e numeração
                                        @endif
                                    </p>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $sessao->camara->nome }}
                                    </td>
                                @endif

                                <td>
                                    {{ $sessao->legislatura->numero }}ª Legislatura
                                </td>

                                <td>
                                    <p class="font-medium text-slate-700 dark:text-neutral-300">
                                        {{ $sessao->data_hora_inicio_previsto->format('d/m/Y') }}

                                        <span class="text-slate-400 dark:text-neutral-600">
                                            às
                                        </span>

                                        {{ $sessao->data_hora_inicio_previsto->format('H:i') }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                                        {{ $sessao->local ?: 'Local a definir' }}
                                    </p>
                                </td>

                                <td>
                                    <x-ui::badge :variant="$variante">
                                        {{ $situacao }}
                                    </x-ui::badge>
                                </td>

                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('view', $sessao)
                                            <a href="{{ route('sessoes.show', $sessao) }}" wire:navigate.hover
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                                   text-sm font-semibold text-indigo-600 transition
                                                   hover:bg-indigo-50 hover:text-indigo-700
                                                   dark:text-indigo-400 dark:hover:bg-indigo-500/10
                                                   dark:hover:text-indigo-300">
                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                                Visualizar
                                            </a>
                                        @endcan

                                        @if ($sessao->situacao === 'em_preparacao')
                                            @can('update', $sessao)
                                                <a href="{{ route('sessoes.edit', $sessao) }}" wire:navigate.hover
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                                       text-sm font-semibold text-slate-600 transition
                                                       hover:bg-slate-100 hover:text-slate-950
                                                       dark:text-neutral-300 dark:hover:bg-neutral-800
                                                       dark:hover:text-white">
                                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                    Editar
                                                </a>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 6 : 5 }}">
                                    <x-ui::empty-state icon="fa-calendar-days">
                                        Nenhuma sessão cadastrada.

                                        <span class="block">
                                            Cadastre uma sessão para iniciar a organização da atividade legislativa.
                                        </span>
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($sessoes->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $sessoes->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
