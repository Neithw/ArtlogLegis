<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Proposições
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
                            Proposições cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte os rascunhos e as proposições protocoladas.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\Proposicao::class)
                            <x-ui::button :href="route('proposicoes.arquivadas')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivadas
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\Proposicao::class)
                            <x-ui::button :href="route('proposicoes.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Nova proposição
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Identificação</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Tipo</th>
                            <th scope="col">Ementa</th>
                            <th scope="col">Situação</th>

                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($proposicoes as $proposicao)
                            @php
                                [$situacao, $variante] = match ($proposicao->situacao) {
                                    'rascunho' => ['Rascunho', 'warning'],
                                    'protocolada' => ['Protocolada', 'success'],
                                    default => [ucfirst(str_replace('_', ' ', $proposicao->situacao)), 'info'],
                                };
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <p class="font-semibold text-slate-950 dark:text-neutral-100">
                                        @if ($proposicao->numero !== null && $proposicao->ano !== null)
                                            Nº {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                        @else
                                            Rascunho #{{ $proposicao->id }}
                                        @endif
                                    </p>

                                    @if ($proposicao->data_protocolo)
                                        <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                                            Protocolada em
                                            <time datetime="{{ $proposicao->data_protocolo->toIso8601String() }}">
                                                {{ $proposicao->data_protocolo->format('d/m/Y H:i') }}
                                            </time>
                                        </p>
                                    @endif
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $proposicao->camara?->nome ?? 'Câmara indisponível' }}
                                    </td>
                                @endif

                                <td>
                                    {{ $proposicao->tipoProposicao?->nome ?? 'Tipo indisponível' }}
                                </td>

                                <td class="max-w-md">
                                    <p class="line-clamp-2">
                                        {{ $proposicao->ementa ?: 'Não informada' }}
                                    </p>
                                </td>

                                <td>
                                    <x-ui::badge :variant="$variante">
                                        {{ $situacao }}
                                    </x-ui::badge>
                                </td>

                                <td class="text-right">
                                    @can('view', $proposicao)
                                        <a href="{{ route('proposicoes.show', $proposicao) }}" wire:navigate.hover
                                            class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                                   text-sm font-semibold text-indigo-600 transition
                                                   hover:bg-indigo-50 hover:text-indigo-700
                                                   dark:text-indigo-400 dark:hover:bg-indigo-500/10
                                                   dark:hover:text-indigo-300">
                                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                            Visualizar
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 6 : 5 }}">
                                    <x-ui::empty-state icon="fa-file-lines">
                                        Nenhuma proposição foi encontrada.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($proposicoes->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $proposicoes->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
