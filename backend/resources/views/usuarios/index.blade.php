<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Usuários
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
                            Usuários cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Gerencie os usuários, seus papéis, permissões e acessos ao sistema.
                        </p>
                    </div>

                    @can('create', App\Models\User::class)
                        <x-ui::button :href="route('usuarios.create')">
                            <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                            Cadastrar Usuário
                        </x-ui::button>
                    @endcan
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Usuário</th>

                            @if ($usuarioIsRoot)
                                <th scope="col">Câmara</th>
                            @endif

                            <th scope="col">Papel</th>
                            <th scope="col">Situação</th>
                            <th scope="col" class="text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($usuarios as $usuario)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div @class([
                                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                                            'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400' => $usuario->isRoot(),
                                            'bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400' => !$usuario->isRoot(),
                                        ])>
                                            <i @class([
                                                'fa-solid',
                                                'fa-user-shield' => $usuario->isRoot(),
                                                'fa-user' => !$usuario->isRoot(),
                                            ]) aria-hidden="true"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="font-semibold text-slate-950 dark:text-neutral-100">
                                                    {{ $usuario->name }}
                                                </p>

                                                @if ($usuario->isRoot())
                                                    <x-ui::badge variant="info">
                                                        Root
                                                    </x-ui::badge>
                                                @endif
                                            </div>

                                            <p class="truncate text-xs text-slate-500 dark:text-neutral-500">
                                                {{ $usuario->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                @if ($usuarioIsRoot)
                                    <td>
                                        <span @class([
                                            'text-sm',
                                            'text-slate-700 dark:text-neutral-300' => $usuario->camara,
                                            'text-slate-500 dark:text-neutral-500' => !$usuario->camara,
                                        ])>
                                            {{ $usuario->camara?->nome ?? 'Acesso global' }}
                                        </span>
                                    </td>
                                @endif

                                <td>
                                    <span class="text-sm text-slate-700 dark:text-neutral-300">
                                        {{ $usuario->role?->nome ?? 'Sem papel definido' }}
                                    </span>
                                </td>

                                <td>
                                    <x-ui::badge :variant="$usuario->ativo ? 'success' : 'neutral'">
                                        {{ $usuario->ativo ? 'Ativo' : 'Inativo' }}
                                    </x-ui::badge>
                                </td>

                                <td>
                                    @if ($usuario->isRoot())
                                        <div
                                            class="flex items-center justify-end gap-2 text-xs font-medium text-slate-500 dark:text-neutral-500">
                                            <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                                            <span>Conta protegida</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-end gap-1">
                                            @can('update', $usuario)
                                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:text-amber-400 dark:hover:bg-amber-950/40"
                                                    aria-label="Editar {{ $usuario->name }}">
                                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                                    <span class="hidden sm:inline">Editar</span>
                                                </a>
                                            @endcan

                                            @if ($usuario->ativo)
                                                @can('desativar', $usuario)
                                                    @if (!auth()->user()->is($usuario))
                                                        <form action="{{ route('usuarios.desativar', $usuario) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Deseja realmente desativar este usuário? O acesso ao sistema será bloqueado.');">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-950/40"
                                                                aria-label="Desativar {{ $usuario->name }}">
                                                                <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                                                <span class="hidden sm:inline">Desativar</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            @else
                                                @can('reativar', $usuario)
                                                    <form action="{{ route('usuarios.reativar', $usuario) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button type="submit"
                                                            class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:text-emerald-400 dark:hover:bg-emerald-950/40"
                                                            aria-label="Reativar {{ $usuario->name }}">
                                                            <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                                            <span class="hidden sm:inline">Reativar</span>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif

                                            @can('delete', $usuario)
                                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                                    onsubmit="return confirm('Deseja realmente excluir este usuário? O registro será arquivado e o acesso permanecerá bloqueado.');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-950/40">
                                                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                                        <span class="hidden sm:inline">Excluir</span>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}">
                                    <x-ui::empty-state icon="fa-users">
                                        Nenhum usuário cadastrado.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>

                @if ($usuarios->hasPages())
                    <footer class="border-t border-slate-200 px-4 py-4 sm:px-6 dark:border-neutral-800">
                        {{ $usuarios->links() }}
                    </footer>
                @endif
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
