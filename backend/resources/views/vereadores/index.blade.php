<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Vereadores
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

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"
                    role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <header
                    class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Vereadores cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os vereadores e seus dados públicos.
                        </p>
                    </div>

                    @can('create', App\Models\Vereador::class)
                        <a href="{{ route('vereadores.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Cadastrar Vereador
                        </a>
                    @endcan
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Nome
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Nome parlamentar
                                </th>

                                @if ($usuarioIsRoot)
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Câmara
                                    </th>
                                @endif

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Conta vinculada
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($vereadores as $vereador)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $vereador->nome }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $vereador->nome_parlamentar ?? 'Não informado' }}
                                        </p>
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $vereador->camara?->nome ?? 'Câmara indisponível' }}
                                            </p>
                                        </td>
                                    @endif

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($vereador->user)
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $vereador->user->name }}
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    {{ $vereador->user->email }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">
                                                Sem conta vinculada
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('view', $vereador)
                                                <a href="{{ route('vereadores.show', $vereador) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
                                                    Visualizar
                                                </a>
                                            @endcan

                                            @can('update', $vereador)
                                                <a href="{{ route('vereadores.edit', $vereador) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('delete', $vereador)
                                                <form action="{{ route('vereadores.destroy', $vereador) }}"
                                                    onsubmit="return confirm('Deseja realmente excluir este vereador?')"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="rounded-lg p-2 text-sm font-semibold text-red-800 transition hover:bg-red-100">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan={{ $usuarioIsRoot ? 5 : 4 }}
                                        class="px-6 py-12 text-center text-sm text-gray-500">
                                        Nenhum vereador foi encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($vereadores->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $vereadores->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
