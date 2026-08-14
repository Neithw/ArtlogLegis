<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Mandatos
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
                            Mandatos cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Gerencie os mandatos e seus vínculos partidários.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\Mandato::class)
                            <x-ui::button :href="route('mandatos.arquivados')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivados
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\Mandato::class)
                            <x-ui::button :href="route('mandatos.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Novo mandato
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Vereador</th>
                            <th scope="col">Legislatura</th>

                            <th scope="col">Período</th>
                            <th scope="col">Situação</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mandatos as $mandato)
                            @php
                                $hoje = now()->startOfDay();

                                if ($hoje->lt($mandato->data_inicio)) {
                                    $situacao = 'Futuro';
                                    $variante = 'info';
                                } elseif ($mandato->data_fim && $hoje->gt($mandato->data_fim)) {
                                    $situacao = 'Encerrado';
                                    $variante = 'neutral';
                                } else {
                                    $situacao = 'Em andamento';
                                    $variante = 'success';
                                }
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                                    </span>
                                </td>

                                <td>
                                    {{ $mandato->legislatura->numero }}ª Legislatura
                                </td>

                                <td>
                                    <time datetime="{{ $mandato->data_inicio->format('Y-m-d') }}">
                                        {{ $mandato->data_inicio->format('d/m/Y') }}
                                    </time>

                                    <span class="sr-only">até</span>

                                    <span aria-hidden="true" class="mx-1 text-slate-400 dark:text-neutral-600">
                                        –
                                    </span>

                                    @if ($mandato->data_fim)
                                        <time datetime="{{ $mandato->data_fim->format('Y-m-d') }}">
                                            {{ $mandato->data_fim->format('d/m/Y') }}
                                        </time>
                                    @else
                                        Em aberto
                                    @endif
                                </td>

                                <td>
                                    <x-ui::badge :variant="$variante">
                                        {{ $situacao }}
                                    </x-ui::badge>
                                </td>

                                <td class="text-right">
                                    <div class="flex items-center justify-end">
                                        @can('view', $mandato)
                                            <a href="{{ route('mandatos.show', $mandato) }}" wire:navigate.hover
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                                       text-sm font-semibold text-indigo-600 transition
                                                       hover:bg-indigo-50 hover:text-indigo-700
                                                       dark:text-indigo-400 dark:hover:bg-indigo-500/10
                                                       dark:hover:text-indigo-300">
                                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                                Visualizar
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-ui::empty-state icon="fa-id-card">
                                        Nenhum mandato foi encontrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($mandatos->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $mandatos->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
