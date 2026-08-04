<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Editar legislatura
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('legislaturas.update', $legislatura) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados da legislatura
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Atualize o número e o período institucional da legislatura.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-700">
                                Câmara
                            </p>

                            <div class="mt-1 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $legislatura->camara->nome }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    A Câmara vinculada não pode ser alterada após o cadastro da legislatura.
                                </p>
                            </div>

                            @error('camara_id')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="numero" value="Número da legislatura" />

                            <x-input id="numero" name="numero" type="number" min="1" max="65535"
                                class="mt-1 block w-full" :value="old('numero', $legislatura->numero)" required autofocus />

                            <p class="mt-1 text-xs text-gray-500">
                                Informe apenas o número, por exemplo: 20.
                            </p>

                            @error('numero')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="data_inicio" value="Data de início" />

                            <x-input id="data_inicio" name="data_inicio" type="date" class="mt-1 block w-full"
                                :value="old('data_inicio', $legislatura->data_inicio->format('Y-m-d'))" required />

                            @error('data_inicio')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="data_fim" value="Data de término" />

                            <x-input id="data_fim" name="data_fim" type="date" class="mt-1 block w-full"
                                :value="old('data_fim', $legislatura->data_fim->format('Y-m-d'))" required />

                            @error('data_fim')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('legislaturas.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Atualizar legislatura
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
