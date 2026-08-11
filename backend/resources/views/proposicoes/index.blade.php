<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Proposições
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
                            Proposições
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie as proposições.
                        </p>
                    </div>

                    <div>
                        @can('viewArchived', \App\Models\Proposicao::class)
                            <a href="{{ route('proposicoes.arquivadas') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-yellow-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-700">
                                Arquivadas
                            </a>
                        @endcan

                        @can('create', \App\Models\Proposicao::class)
                            <a href="{{ route('proposicoes.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                Nova Proposição
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
                                    Identificação
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Tipo
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Ementa
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Legislatura
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Situação
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
                            @forelse ($proposicoes as $proposicao)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            Rascunho #{{ $proposicao->id }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $proposicao->tipoProposicao->nome }}
                                    </td>

                                    <td class="max-w-md px-6 py-4 text-sm text-gray-700">
                                        <p class="line-clamp-3">
                                            {{ $proposicao->ementa ?: 'Não informada' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $proposicao->legislatura->numero }}ª Legislatura
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ ucfirst(str_replace('_', ' ', $proposicao->situacao)) }}
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $proposicao->camara->nome }}
                                        </td>
                                    @endif

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('view', $proposicao)
                                                <a href="{{ route('proposicoes.show', $proposicao) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-green-700 transition hover:bg-green-50">
                                                    Visualizar
                                                </a>
                                            @endcan

                                            @can('update', $proposicao)
                                                <a href="{{ route('proposicoes.edit', $proposicao) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('delete', $proposicao)
                                                <form action="{{ route('proposicoes.destroy', $proposicao) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja realmente arquivar esta proposição?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="rounded-lg p-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                        Arquivar
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $usuarioIsRoot ? 7 : 6 }}" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-gray-700">
                                            Nenhuma proposição cadastrada.
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Cadastre uma proposição para iniciar.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($proposicoes->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $proposicoes->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
