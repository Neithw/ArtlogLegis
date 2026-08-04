<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Usuários
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <header
                    class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Usuários cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os usuários, seus papéis e seus acessos ao sistema.
                        </p>
                    </div>

                    @can('usuarios:criar')
                        <a href="{{ route('usuarios.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Cadastrar Usuário
                        </a>
                    @endcan
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Usuário
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Câmara
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Papel
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $usuario->name }}
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                {{ $usuario->email }}
                                            </p>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $usuario->camara?->nome ?? 'Acesso global' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $usuario->role?->nome ?? 'Sem papel definido' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($usuario->ativo)
                                            <span
                                                class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Ativo
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right">
                                        @if (!$usuario->isRoot())
                                            <div class="flex items-center justify-end gap-2">
                                                @can('usuarios:editar')
                                                    <a href="{{ route('usuarios.edit', $usuario) }}"
                                                        class="rounded-lg p-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                        Editar
                                                    </a>
                                                @endcan

                                                @if ($usuario->ativo)
                                                    @can('usuarios:desativar')
                                                        @if (!auth()->user()->is($usuario))
                                                            <form action="{{ route('usuarios.desativar', $usuario) }}"
                                                                onsubmit="return confirm('Deseja realmente desativar este usuário?');"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')

                                                                <button type="submit"
                                                                    class="rounded-lg p-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                                    Desativar
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                @else
                                                    @can('usuarios:reativar')
                                                        <form action="{{ route('usuarios.reativar', $usuario) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                class="rounded-lg p-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
                                                                Reativar
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif

                                                @if (auth()->user()->isRoot())
                                                    @can('usuarios:excluir')
                                                        <form action="{{ route('usuarios.destroy', $usuario) }}"
                                                            onsubmit="return confirm('Deseja realmente excluir este usuário?')"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                class="rounded-lg p-2 text-sm font-semibold text-red-800 transition hover:bg-red-100">
                                                                Excluir
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                        Nenhum usuário foi encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($usuarios->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
