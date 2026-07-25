<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Câmaras
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
                            Câmaras cadastradas
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Consulte e gerencie as Câmaras disponíveis no sistema.
                        </p>
                    </div>

                    @can('camaras.criar')
                        <a href="{{ route('camaras.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Cadastrar Câmara
                        </a>
                    @endcan
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Câmara
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    CNPJ
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

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($camaras as $camara)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-indigo-600">
                                                #{{ $camaras->firstItem() + $loop->index }}
                                            </span>
                                            <span class="font-semibold text-gray-900">
                                                {{ $camara->nome }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $camara->cnpj ?? 'Não informado' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($camara->ativo)
                                            <span
                                                class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                Ativa
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                                Inativa
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('camaras.editar')
                                                <a href="{{ route('camaras.edit', $camara) }}"
                                                    class="rounded-lg px-2 py-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('camaras.excluir')
                                                <form action="{{ route('camaras.destroy', $camara) }}" method="POST"
                                                    onsubmit="return confirm('Deseja realmente excluir esta Câmara?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="rounded-lg px-2 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <p class="font-semibold text-gray-700">
                                            Nenhuma Câmara cadastrada
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Cadastre uma Câmara para começar a utilizar o sistema.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($camaras->hasPages())
                    <footer class="border-t border-gray-200 px-6 py-4">
                        {{ $camaras->links() }}
                    </footer>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
