<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Mandatos arquivados
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
                            Mandatos arquivados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte e restaure os mandatos arquivados.
                        </p>
                    </div>

                    <x-ui::button :href="route('mandatos.index')" variant="secondary">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Voltar
                    </x-ui::button>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Vereador</th>
                            <th scope="col">Legislatura</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Partido</th>
                            <th scope="col">Período</th>
                            <th scope="col">Arquivado em</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($arquivados as $mandato)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                                    </span>
                                </td>

                                <td>
                                    {{ $mandato->legislatura->numero }}ª Legislatura
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $mandato->legislatura->camara?->nome ?? 'Câmara indisponível' }}
                                    </td>
                                @endif

                                <td>
                                    {{ $mandato->ultimaFiliacaoPartidaria?->partido?->sigla ?? 'Sem partido' }}
                                </td>

                                <td>
                                    {{ $mandato->data_inicio->format('d/m/Y') }}

                                    <span class="mx-1 text-slate-400 dark:text-neutral-600">
                                        –
                                    </span>
                                    {{ $mandato->data_fim?->format('d/m/Y') ?? 'Em aberto' }}
                                </td>

                                <td>
                                    <time datetime="{{ $mandato->deleted_at->toIso8601String() }}">
                                        {{ $mandato->deleted_at->format('d/m/Y H:i') }}
                                    </time>
                                </td>

                                <td class="text-right">
                                    @can('restore', $mandato)
                                        <form action="{{ route('mandatos.restore', $mandato) }}" method="POST"
                                            onsubmit="return confirm('Deseja realmente restaurar este mandato?')"
                                            class="flex justify-end">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 7 : 6 }}">
                                    <x-ui::empty-state icon="fa-box-archive">
                                        Nenhum mandato arquivado foi encontrado.
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
