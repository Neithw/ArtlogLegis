<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Legislaturas
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
                            Legislaturas cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{ $usuarioIsRoot
                                ? 'Gerencie os períodos legislativos das Câmaras.'
                                : 'Gerencie os períodos da atividade parlamentar.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\Legislatura::class)
                            <x-ui::button :href="route('legislaturas.arquivadas')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivadas
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\Legislatura::class)
                            <x-ui::button :href="route('legislaturas.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Nova legislatura
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Legislatura</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Período</th>
                            <th scope="col">Situação</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($legislaturas as $legislatura)
                            @php
                                $hoje = now()->startOfDay();

                                if ($hoje->lt($legislatura->data_inicio)) {
                                    $situacao = 'Futura';
                                    $variante = 'info';
                                } elseif ($hoje->gt($legislatura->data_fim)) {
                                    $situacao = 'Encerrada';
                                    $variante = 'neutral';
                                } else {
                                    $situacao = 'Em andamento';
                                    $variante = 'success';
                                }
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $legislatura->numero }}ª Legislatura
                                    </span>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $legislatura->camara->nome }}
                                    </td>
                                @endif

                                <td>
                                    {{ $legislatura->data_inicio->format('d/m/Y') }}

                                    <span class="sr-only">até</span>

                                    <span aria-hidden="true" class="mx-1 text-slate-400 dark:text-neutral-600">
                                        –
                                    </span>

                                    {{ $legislatura->data_fim->format('d/m/Y') }}
                                </td>

                                <td>
                                    <x-ui::badge :variant="$variante">
                                        {{ $situacao }}
                                    </x-ui::badge>
                                </td>

                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('update', $legislatura)
                                            <a href="{{ route('legislaturas.edit', $legislatura) }}" wire:navigate.hover
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-950/40">
                                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                <span class="hidden sm:inline">Editar</span>
                                            </a>
                                        @endcan

                                        @can('delete', $legislatura)
                                            <form action="{{ route('legislaturas.destroy', $legislatura) }}" method="POST"
                                                onsubmit="return confirm('Deseja realmente arquivar esta legislatura?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-950/40">
                                                    <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                                    Arquivar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}">
                                    <x-ui::empty-state icon="fa-calendar-days">
                                        Nenhuma legislatura cadastrada.

                                        <span class="block">
                                            Cadastre uma legislatura para definir o período da atividade parlamentar.
                                        </span>
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($legislaturas->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $legislaturas->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
