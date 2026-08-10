<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Tipos de Proposição Arquivados
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
                            Tipos de Proposição arquivados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os tipos de proposição arquivados.
                        </p>
                    </div>

                    <div>
                        @can('viewAny', \App\Models\TipoProposicao::class)
                            <a href="{{ route('tipos-proposicao.index') }}"
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
                            @forelse ($tiposProposicao as $tipoProposicao)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $tipoProposicao->nome }}
                                        </div>
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $tipoProposicao->camara->nome }}
                                        </td>
                                    @endif

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('restore', $tipoProposicao)
                                                <form action="{{ route('tipos-proposicao.restore', $tipoProposicao) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja realmente restaurar este tipo de proposição?');">
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
                                    <td colspan="{{ $usuarioIsRoot ? 3 : 2 }}" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-gray-700">
                                            Nenhum tipo de proposição arquivado.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tiposProposicao->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $tiposProposicao->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
