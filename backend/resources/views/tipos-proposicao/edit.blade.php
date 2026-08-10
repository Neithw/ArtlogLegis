<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Editar tipo de proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('tipos-proposicao.update', $tipoProposicao) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados do tipo
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Atualize os dados do tipo.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-700">
                                Câmara
                            </p>

                            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $tipoProposicao->camara->nome }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    A Câmara vinculada não pode ser alterada após o cadastro do tipo.
                                </p>
                            </div>

                            @error('camara_id')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="nome" value="Nome do tipo" />

                            <x-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                                :value="old('nome', $tipoProposicao->nome)" required autofocus />

                            @error('nome')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('tipos-proposicao.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Atualizar tipo
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
