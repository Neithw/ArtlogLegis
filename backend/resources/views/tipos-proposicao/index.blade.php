<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Tipos de proposição
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
                            Tipos cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Gerencie os tipos utilizados na classificação das proposições.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\TipoProposicao::class)
                            <x-ui::button :href="route('tipos-proposicao.arquivados')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivados
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\TipoProposicao::class)
                            <x-ui::button :href="route('tipos-proposicao.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Novo tipo
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($tiposProposicao as $tipoProposicao)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $tipoProposicao->nome }}
                                    </span>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $tipoProposicao->camara?->nome ?? 'Câmara indisponível' }}
                                    </td>
                                @endif

                                <td class="text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-1">
                                        @can('update', $tipoProposicao)
                                            <a href="{{ route('tipos-proposicao.edit', $tipoProposicao) }}"
                                                wire:navigate.hover
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-950/40">
                                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                Editar
                                            </a>
                                        @endcan

                                        @can('delete', $tipoProposicao)
                                            <form action="{{ route('tipos-proposicao.destroy', $tipoProposicao) }}"
                                                method="POST"
                                                onsubmit="return confirm('Deseja realmente arquivar este tipo de proposição?')">
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
                                <td colspan="{{ $usuarioIsRoot ? 3 : 2 }}">
                                    <x-ui::empty-state icon="fa-file-lines">
                                        Nenhum tipo de proposição foi encontrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($tiposProposicao->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $tiposProposicao->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
