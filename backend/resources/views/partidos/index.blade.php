<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Partidos
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div role="status"
                    class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800
                           dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>

                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Partidos cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte os partidos e seus dados públicos.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\Partido::class)
                            <x-ui::button :href="route('partidos.arquivados')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivados
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\Partido::class)
                            <x-ui::button :href="route('partidos.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Novo partido
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Sigla</th>
                            <th scope="col">Número eleitoral</th>

                            @if ($usuarioIsRoot)
                                <th scope="col" class="text-right">
                                    Ações
                                </th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($partidos as $partido)
                            <tr
                                class="transition-colors hover:bg-slate-50
                                       dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-medium text-slate-950 dark:text-neutral-100">
                                        {{ $partido->nome }}
                                    </span>
                                </td>

                                <td>
                                    <span class="font-medium text-slate-950 dark:text-neutral-100">
                                        {{ $partido->sigla }}
                                    </span>
                                </td>

                                <td>
                                    <span class="font-medium text-slate-950 dark:text-neutral-100">
                                        {{ $partido->numero_eleitoral ?? '–' }}
                                    </span>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @can('update', $partido)
                                                <a href="{{ route('partidos.edit', $partido) }}" wire:navigate.hover
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-950/40">
                                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('delete', $partido)
                                                <form action="{{ route('partidos.destroy', $partido) }}" method="POST"
                                                    onsubmit="return confirm('Deseja realmente arquivar este partido?')">
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
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 4 : 3 }}">
                                    <x-ui::empty-state icon="fa-flag">
                                        Nenhum partido foi encontrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($partidos->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $partidos->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
