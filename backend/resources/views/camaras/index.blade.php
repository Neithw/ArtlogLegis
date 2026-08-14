<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Câmaras
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert>
                    {{ session('success') }}
                </x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error">
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
                            Câmaras cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte os dados institucionais e gerencie a situação das Câmaras.
                        </p>
                    </div>

                    @can('create', App\Models\Camara::class)
                        <x-ui::button :href="route('camaras.create')">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Cadastrar Câmara
                        </x-ui::button>
                    @endcan
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Câmara</th>
                            <th scope="col">CNPJ</th>
                            <th scope="col">Situação</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($camaras as $camara)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                                            <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                                        </div>

                                        <div>
                                            <p class="font-semibold text-slate-950 dark:text-neutral-100">
                                                {{ $camara->nome }}
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-neutral-500">
                                                Identificador #{{ $camara->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span @class([
                                        'text-sm',
                                        'text-slate-700 dark:text-neutral-300' => $camara->cnpj,
                                        'text-slate-500 dark:text-neutral-500' => !$camara->cnpj,
                                    ])>
                                        {{ $camara->cnpj_formatado ?? 'Não informado' }}
                                    </span>
                                </td>

                                <td>
                                    <x-ui::badge :variant="$camara->ativo ? 'success' : 'neutral'">
                                        {{ $camara->ativo ? 'Ativa' : 'Inativa' }}
                                    </x-ui::badge>
                                </td>

                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @can('update', $camara)
                                            <a href="{{ route('camaras.edit', $camara) }}"
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-neutral-100"
                                                aria-label="Editar {{ $camara->nome }}">
                                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                <span class="hidden sm:inline">Editar</span>
                                            </a>
                                        @endcan

                                        @if ($camara->ativo)
                                            @can('desativar', $camara)
                                                <form action="{{ route('camaras.desativar', $camara) }}" method="POST"
                                                    onsubmit="return confirm('Deseja realmente desativar esta Câmara?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-950/40"
                                                        aria-label="Desativar {{ $camara->nome }}">
                                                        <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                                        <span class="hidden sm:inline">Desativar</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        @else
                                            @can('reativar', $camara)
                                                <form action="{{ route('camaras.reativar', $camara) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:text-emerald-400 dark:hover:bg-emerald-950/40"
                                                        aria-label="Reativar {{ $camara->nome }}">
                                                        <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                                        <span class="hidden sm:inline">Reativar</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui::empty-state icon="fa-building-columns">
                                        Nenhuma Câmara cadastrada.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($camaras->hasPages())
                    <footer class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $camaras->links() }}
                    </footer>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
