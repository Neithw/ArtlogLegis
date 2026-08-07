<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Partidos
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
                            Partidos cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Consulte os partidos e seus dados públicos.
                        </p>
                    </div>

                    <div>
                        @can('viewArchived', \App\Models\Partido::class)
                            <a href="{{ route('partidos.arquivados') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                                Arquivados
                            </a>
                        @endcan

                        @can('create', \App\Models\Partido::class)
                            <a href="{{ route('partidos.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                Novo Partido
                            </a>
                        @endcan
                    </div>
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
                                    Sigla
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Número Eleitoral
                                </th>

                                @if ($usuarioIsRoot)
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Ações
                                    </th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($partidos as $partido)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $partido->nome }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $partido->sigla }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $partido->numero_eleitoral ?? '-' }}
                                        </p>
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-4 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @can('update', $partido)
                                                    <a href="{{ route('partidos.edit', $partido) }}"
                                                        class="rounded-lg p-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                        Editar
                                                    </a>
                                                @endcan

                                                @can('delete', $partido)
                                                    <form action="{{ route('partidos.destroy', $partido) }}"
                                                        onsubmit="return confirm('Deseja realmente arquivar este partido?')"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                            class="rounded-lg p-2 text-sm font-semibold text-red-800 transition hover:bg-red-100">
                                                            Arquivar
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $usuarioIsRoot ? 4 : 3 }}"
                                        class="px-6 py-12 text-center text-sm text-gray-500">
                                        Nenhum partido foi encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($partidos->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $partidos->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
