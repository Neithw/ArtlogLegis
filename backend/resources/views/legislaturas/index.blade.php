<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Legislaturas
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

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <header
                    class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Legislaturas
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Gerencie os períodos legislativos das Câmaras.
                        </p>
                    </div>

                    @can('create', \App\Models\Legislatura::class)
                        <a href="{{ route('legislaturas.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Nova legislatura
                        </a>
                    @endcan
                </header>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Legislatura
                                </th>

                                @if ($usuarioIsRoot)
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                        Câmara
                                    </th>
                                @endif

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Período
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Situação
                                </th>

                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($legislaturas as $legislatura)
                                @php
                                    $hoje = now()->startOfDay();

                                    if ($hoje->lt($legislatura->data_inicio)) {
                                        $situacao = 'Futura';
                                    } elseif ($hoje->gt($legislatura->data_fim)) {
                                        $situacao = 'Encerrada';
                                    } else {
                                        $situacao = 'Em andamento';
                                    }
                                @endphp

                                <tr class="transition hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-semibold text-gray-900">
                                            {{ $legislatura->numero }}ª Legislatura
                                        </div>
                                    </td>

                                    @if ($usuarioIsRoot)
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $legislatura->camara->nome }}
                                        </td>
                                    @endif

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $legislatura->data_inicio->format('d/m/Y') }}
                                        <span class="text-gray-400">até</span>
                                        {{ $legislatura->data_fim->format('d/m/Y') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span @class([
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                            'bg-blue-100 text-blue-700' => $situacao === 'Futura',
                                            'bg-green-100 text-green-700' => $situacao === 'Em andamento',
                                            'bg-gray-100 text-gray-700' => $situacao === 'Encerrada',
                                        ])>
                                            {{ $situacao }}
                                        </span>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('update', $legislatura)
                                                <a href="{{ route('legislaturas.edit', $legislatura) }}"
                                                    class="rounded-lg p-2 text-sm font-semibold text-yellow-700 transition hover:bg-yellow-50">
                                                    Editar
                                                </a>
                                            @endcan

                                            @can('delete', $legislatura)
                                                <form action="{{ route('legislaturas.destroy', $legislatura) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja realmente excluir esta legislatura?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="rounded-lg p-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $usuarioIsRoot ? 5 : 4 }}" class="px-6 py-12 text-center">
                                        <p class="text-sm font-medium text-gray-700">
                                            Nenhuma legislatura cadastrada.
                                        </p>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Cadastre uma legislatura para iniciar o contexto temporal da atividade
                                            parlamentar.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($legislaturas->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $legislaturas->links() }}
                    </div>
                @endif
        </div>
    </div>
    </div>
</x-app-layout>
