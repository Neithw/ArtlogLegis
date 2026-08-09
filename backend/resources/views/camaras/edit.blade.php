<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Edição
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-gray-900">
                Câmaras
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <header class="border-b border-gray-200 px-6 py-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Editar {{ $camara->nome }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            Atualize os dados cadastrais da Câmara.
                        </p>
                    </div>
                </header>

                <form action="{{ route('camaras.update', $camara) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4 p-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700">
                                Nome da Câmara
                            </label>
                            <input type="text" name="nome" id="nome" value="{{ old('nome', $camara->nome) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition focus:border-indigo-500"
                                placeholder="Ex.: Câmara Municipal de Uberlândia">

                            @error('nome')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="cnpj" class="block text-sm font-medium text-gray-700">
                                CNPJ
                            </label>
                            <p class="text-xs text-gray-500">
                                Informe o CNPJ da instituição, quando disponível.
                            </p>
                            <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj', $camara->cnpj) }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm transition focus:border-indigo-500"
                                placeholder="00.000.000/0000-00">

                            @error('cnpj')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <footer
                        class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-end">

                        <a href="{{ route('camaras.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Salvar alterações
                        </button>
                    </footer>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
