<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Partidos arquivados
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status"
                    class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200
                           bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800
                           dark:border-emerald-500/20 dark:bg-emerald-500/10
                           dark:text-emerald-300">
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
                            Partidos arquivados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte e restaure os partidos arquivados.
                        </p>
                    </div>

                    <x-ui::button :href="route('partidos.index')" variant="secondary">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Voltar
                    </x-ui::button>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Sigla</th>
                            <th scope="col">Número eleitoral</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($arquivados as $partido)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
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

                                <td class="text-right">
                                    <div class="flex items-center justify-end">
                                        @can('restore', $partido)
                                            <form action="{{ route('partidos.restore', $partido) }}" method="POST"
                                                onsubmit="return confirm('Deseja realmente restaurar este partido?')">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2
                                                           text-sm font-semibold text-emerald-600 transition
                                                           hover:bg-emerald-50 hover:text-emerald-700
                                                           dark:text-emerald-400 dark:hover:bg-emerald-500/10
                                                           dark:hover:text-emerald-300">
                                                    <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                                    Restaurar
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui::empty-state icon="fa-box-archive">
                                        Nenhum partido arquivado foi encontrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($arquivados->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $arquivados->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
