<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Mandatos
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
                            Mandatos cadastrados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os mandatos e seus dados.
                        </p>
                    </div>
                    <div>
                        @can('viewArchived', App\Models\Mandato::class)
                            <a href="{{ route('mandatos.arquivados') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-yellow-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-700">
                                Arquivados
                            </a>
                        @endcan
                        @can('create', App\Models\Mandato::class)
                            <a href="{{ route('mandatos.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                Novo Mandato
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
                                    Vereador
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Legislatura
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Câmara
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Partido
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Início
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Fim
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($mandatos as $mandato)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->legislatura->numero }}ª Legislatura
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->legislatura->camara->nome ?? 'Câmara indisponível' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->ultimaFiliacaoPartidaria?->partido->sigla ?? 'Sem partido' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->data_inicio->format('d/m/Y') }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $mandato->data_fim?->format('d/m/Y') ?? 'Não encerrado' }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-4 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('update', $mandato)
                                                <a href="{{ route('mandatos.troca-partidaria.create', $mandato) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
                                                    Trocar partido
                                                </a>

                                                <a href="{{ route('mandatos.edit', $mandato) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-yellow-600 transition hover:bg-yellow-50">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('delete', $mandato)
                                                <form action="{{ route('mandatos.destroy', $mandato) }}"
                                                    onsubmit="return confirm('Deseja realmente arquivar este mandato?')"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="rounded-lg p-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                                                        Arquivar
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                        Nenhum mandato foi encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($mandatos->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $mandatos->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
