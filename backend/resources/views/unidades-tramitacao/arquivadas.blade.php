<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Unidades de tramitação arquivadas
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

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Unidades arquivadas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte e restaure as unidades de tramitação arquivadas.
                        </p>
                    </div>

                    <x-ui::button :href="route('unidades-tramitacao.index')" variant="secondary" wire:navigate.hover>
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Voltar
                    </x-ui::button>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Unidade</th>
                            <th scope="col">Tipo</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Arquivada em</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($unidadesTramitacao as $unidadeTramitacao)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $unidadeTramitacao->nome }}
                                    </span>

                                    @if ($unidadeTramitacao->sigla)
                                        <span class="mt-1 block text-xs text-slate-500 dark:text-neutral-500">
                                            {{ $unidadeTramitacao->sigla }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <x-ui::badge>
                                        {{ $tiposLabels[$unidadeTramitacao->tipo] ?? $unidadeTramitacao->tipo }}
                                    </x-ui::badge>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $unidadeTramitacao->camara->nome }}
                                    </td>
                                @endif

                                <td>
                                    <time datetime="{{ $unidadeTramitacao->deleted_at->toIso8601String() }}">
                                        {{ $unidadeTramitacao->deleted_at->format('d/m/Y H:i') }}
                                    </time>
                                </td>

                                <td class="text-right">
                                    @can('restore', $unidadeTramitacao)
                                        <form action="{{ route('unidades-tramitacao.restore', $unidadeTramitacao) }}"
                                            method="POST"
                                            onsubmit="return confirm('Deseja realmente restaurar esta unidade de tramitação?')"
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
                                <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}">
                                    <x-ui::empty-state icon="fa-box-archive">
                                        Nenhuma unidade de tramitação arquivada foi encontrada.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($unidadesTramitacao->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $unidadesTramitacao->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
