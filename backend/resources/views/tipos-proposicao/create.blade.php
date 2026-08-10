<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Novo tipo de proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('tipos-proposicao.store') }}" class="space-y-6">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados do tipo
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados que compõem o tipo de proposição.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        @if ($usuarioIsRoot)
                            <div class="md:col-span-2">
                                <x-label for="camara_id" value="Câmara" />

                                <select id="camara_id" name="camara_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">
                                        Selecione uma Câmara
                                    </option>

                                    @foreach ($camaras as $camara)
                                        <option value="{{ $camara->id }}" @selected(old('camara_id') == $camara->id)>
                                            {{ $camara->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('camara_id')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @else
                            <div class="md:col-span-2">
                                <x-label value="Câmara" />

                                <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $camaras->first()?->nome ?? 'Câmara não encontrada' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        O tipo será vinculado automaticamente à sua Câmara.
                                    </p>
                                </div>

                                @error('camara_id')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <x-label for="nome" value="Nome do tipo" />

                            <x-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                                :value="old('nome')" required autofocus />

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
                        Cadastrar tipo
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
