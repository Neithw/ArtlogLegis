<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Troca partidária
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">


            <form action="{{ route('mandatos.troca-partidaria.store', $mandato) }}" method="POST" class="space-y-6">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Registrar nova troca partidária
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados para a troca partidária.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div>
                            <x-label for="vereador_id" value="Vereador" />
                            <p>
                                {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                            </p>
                        </div>

                        <div>
                            <x-label for="legislatura_id" value="Legislatura" />

                            <p>
                                {{ $mandato->legislatura->numero }}ª Legislatura
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <x-label value="Partido atual" />

                            <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $mandato->ultimaFiliacaoPartidaria->partido->sigla }}
                                    -
                                    {{ $mandato->ultimaFiliacaoPartidaria->partido->nome }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Início da filiação -
                                    {{ $mandato->ultimaFiliacaoPartidaria->data_inicio->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <x-label for="partido_id" value="Novo Partido" />

                            <select name="partido_id" id="partido_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>

                                <option value="">
                                    Selecione um partido
                                </option>

                                @foreach ($partidos as $partido)
                                    <option value="{{ $partido->id }}" @selected(old('partido_id') == $partido->id)>
                                        {{ $partido->sigla }} - {{ $partido->nome }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="partido_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="data_troca" value="Data da troca" />

                            <x-input id="data_troca" name="data_troca" type="date" class="mt-1 block w-full"
                                :value="old('data_troca')" required />

                            <x-input-error for="data_troca" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('mandatos.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Registrar troca
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
