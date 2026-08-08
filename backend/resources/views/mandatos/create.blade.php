<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Novo mandato
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">


            <form action="{{ route('mandatos.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados do mandato
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados do mandato.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-label for="vereador_id" value="Vereador" />

                            <select name="vereador_id" id="vereador_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">
                                    Selecione um vereador
                                </option>

                                @foreach ($vereadores as $vereador)
                                    <option value="{{ $vereador->id }}" @selected(old('vereador_id') == $vereador->id)>
                                        {{ $vereador->nome_parlamentar }}

                                        @if ($usuarioIsRoot)
                                            - {{ $vereador->camara->nome }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="vereador_id" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="legislatura_id" value="Legislatura" />

                            <select name="legislatura_id" id="legislatura_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>
                                <option value="">
                                    Selecione uma legislatura
                                </option>

                                @foreach ($legislaturas as $legislatura)
                                    <option value="{{ $legislatura->id }}" @selected(old('legislatura_id') == $legislatura->id)>
                                        {{ $legislatura->numero }}ª Legislatura

                                        @if ($usuarioIsRoot)
                                            - {{ $legislatura->camara->nome }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="legislatura_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="data_inicio" value="Data de início" />

                            <x-input id="data_inicio" name="data_inicio" type="date" class="mt-1 block w-full"
                                :value="old('data_inicio')" required />

                            <x-input-error for="data_inicio" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="data_fim" value="Data de término" />

                            <x-input id="data_fim" name="data_fim" type="date" class="mt-1 block w-full"
                                :value="old('data_fim')" />

                            <x-input-error for="data_fim" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('mandatos.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Cadastrar mandato
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
