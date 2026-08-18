<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Vereadores
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
                            Vereadores cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Gerencie os vereadores e seus dados públicos.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @can('viewArchived', \App\Models\Vereador::class)
                            <x-ui::button :href="route('vereadores.arquivados')" variant="secondary">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivados
                            </x-ui::button>
                        @endcan

                        @can('create', \App\Models\Vereador::class)
                            <x-ui::button :href="route('vereadores.create')">
                                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                                Novo vereador
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Nome parlamentar</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Conta vinculada</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($vereadores as $vereador)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $vereador->nome }}
                                    </span>
                                </td>

                                <td>
                                    {{ $vereador->nome_parlamentar ?? 'Não informado' }}
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        {{ $vereador->camara?->nome ?? 'Câmara indisponível' }}
                                    </td>
                                @endif

                                <td>
                                    @if ($vereador->user)
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <p class="font-medium text-slate-950 dark:text-neutral-100">
                                                    {{ $vereador->user->name }}
                                                </p>

                                                <p class="text-xs text-slate-500 dark:text-neutral-500">
                                                    {{ $vereador->user->email }}
                                                </p>
                                            </div>

                                            @if ($vereador->user->trashed())
                                                <x-ui::badge variant="danger">
                                                    Arquivada
                                                </x-ui::badge>
                                            @elseif (!$vereador->user->ativo)
                                                <x-ui::badge variant="warning">
                                                    Desativada
                                                </x-ui::badge>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-slate-500 dark:text-neutral-500">
                                            Sem conta vinculada
                                        </span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('view', $vereador)
                                            <a href="{{ route('vereadores.show', $vereador) }}" wire:navigate.hover
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
                                <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}">
                                    <x-ui::empty-state icon="fa-user-tie">
                                        Nenhum vereador foi encontrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($vereadores->hasPages())
                    <div class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $vereadores->onEachSide(1)->links() }}
                    </div>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
