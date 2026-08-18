<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Unidades de tramitação
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

            @error('unidadeTramitacao')
                <x-ui::alert type="error" class="mb-6">
                    {{ $message }}
                </x-ui::alert>
            @enderror

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Unidades cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Gerencie as unidades responsáveis pelo fluxo das proposições.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\UnidadeTramitacao::class)
                            <x-ui::button :href="route('unidades-tramitacao.arquivadas')" variant="secondary" wire:navigate.hover>
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivadas
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\UnidadeTramitacao::class)
                            <x-ui::button :href="route('unidades-tramitacao.create')" wire:navigate.hover>
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Nova unidade
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Unidade</th>
                            <th scope="col">Tipo</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

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

                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('update', $unidadeTramitacao)
                                            <a href="{{ route('unidades-tramitacao.edit', $unidadeTramitacao) }}"
                                                wire:navigate.hover
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-950/40">
                                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                Editar
                                            </a>
                                        @endcan

                                        @can('delete', $unidadeTramitacao)
                                            <form action="{{ route('unidades-tramitacao.destroy', $unidadeTramitacao) }}"
                                                method="POST"
                                                onsubmit="return confirm('Deseja realmente arquivar esta unidade de tramitação?')">
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
                                <td colspan="{{ $usuarioIsRoot ? 4 : 3 }}">
                                    <x-ui::empty-state icon="fa-building">
                                        Nenhuma unidade de tramitação cadastrada.

                                        <span class="block">
                                            Cadastre uma unidade para organizar o fluxo das proposições.
                                        </span>
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
