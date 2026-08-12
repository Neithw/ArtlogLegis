<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Unidades de Tramitação Arquivadas
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
                    role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
                    role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <header
                    class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Unidades de Tramitação Arquivadas
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie as unidades arquivadas.
                        </p>
                    </div>

                    <div>
                        @can('viewAny', \App\Models\UnidadeTramitacao::class)
                            <a href="{{ route('unidades-tramitacao.index') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                Voltar
                            </a>
                        @endcan
                    </div>
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Nome
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Sigla
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Tipo
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Arquivada em
                                </th>

                                @if ($usuarioIsRoot)
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Câmara
                                    </th>
                                @endif

                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($unidadesTramitacao as $unidadeTramitacao)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $unidadeTramitacao->nome }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $unidadeTramitacao->sigla ?: 'Não informada' }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $tiposLabels[$unidadeTramitacao->tipo] ?? $unidadeTramitacao->tipo }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ $unidadeTramitacao->deleted_at->format('d/m/Y') }}
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $unidadeTramitacao->camara->nome }}
                                        </td>
                                    @endif

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end">
                                            @can('restore', $unidadeTramitacao)
                                                <form
                                                    action="{{ route('unidades-tramitacao.restore', $unidadeTramitacao) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja realmente restaurar esta unidade de tramitação?');">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="rounded-lg p-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $usuarioIsRoot ? 6 : 5 }}" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-gray-700">
                                            Nenhuma unidade de tramitação arquivada.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($unidadesTramitacao->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $unidadesTramitacao->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
