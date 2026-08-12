<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Editar unidade de tramitação
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('unidades-tramitacao.update', $unidadeTramitacao) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados da unidade
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Atualize os dados da unidade.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-700">
                                Câmara
                            </p>

                            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $unidadeTramitacao->camara->nome }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    A Câmara vinculada não pode ser alterada após o cadastro da unidade de tramitação.
                                </p>
                            </div>

                            <x-input-error for="camara_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="nome" value="Nome da unidade" />

                            <x-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                                :value="old('nome', $unidadeTramitacao->nome)" maxlength="255" required />

                            <x-input-error for="nome" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="sigla" value="Sigla" />

                            <x-input id="sigla" name="sigla" type="text" class="mt-1 block w-full"
                                :value="old('sigla', $unidadeTramitacao->sigla)" maxlength="50" />

                            <x-input-error for="sigla" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="tipo" value="Tipo" />

                            <select id="tipo" name="tipo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">Selecione um tipo</option>

                                @foreach ($tiposLabels as $valor => $label)
                                    <option value="{{ $valor }}" @selected(old('tipo', $unidadeTramitacao->tipo) === $valor)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="tipo" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="descricao" value="Descrição" />

                            <textarea id="descricao" name="descricao" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('descricao', $unidadeTramitacao->descricao) }}</textarea>

                            <x-input-error for="descricao" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('unidades-tramitacao.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Atualizar unidade
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
