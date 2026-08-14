<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Vereadores arquivados
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
                            Vereadores arquivados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Consulte e restaure os vereadores arquivados.
                        </p>
                    </div>

                    <x-ui::button :href="route('vereadores.index')" variant="secondary">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Voltar
                    </x-ui::button>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Nome parlamentar</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Arquivado em</th>
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
                                    <time datetime="{{ $vereador->deleted_at->toIso8601String() }}">
                                        {{ $vereador->deleted_at->format('d/m/Y H:i') }}
                                    </time>
                                </td>

                                <td class="text-right">
                                    <div class="flex items-center justify-end">
                                        @can('restore', $vereador)
                                            <form action="{{ route('vereadores.restore', $vereador) }}" method="POST"
                                                onsubmit="return confirm('Deseja realmente restaurar este vereador?')">
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
                                <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}">
                                    <x-ui::empty-state icon="fa-box-archive">
                                        Nenhum vereador arquivado foi encontrado.
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
