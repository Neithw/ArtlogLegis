<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Novo partido
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('partidos.store') }}" class="space-y-6">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados do partido
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe a identificação e os dados públicos do partido.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div>
                            <x-label for="nome" value="Nome do partido" />

                            <x-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                                :value="old('nome')" required autofocus />

                            <x-input-error for="nome" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="sigla" value="Sigla do partido" />

                            <x-input id="sigla" name="sigla" type="text" class="mt-1 block w-full"
                                :value="old('sigla')" required />

                            <x-input-error for="sigla" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="numero_eleitoral" value="Número eleitoral do partido" />

                            <x-input id="numero_eleitoral" name="numero_eleitoral" type="number" min="1"
                                max="65535" class="mt-1 block w-full" :value="old('numero_eleitoral')" />

                            <p class="mt-1 text-xs text-gray-500">
                                Informe apenas o número, por exemplo: 20.
                            </p>

                            <x-input-error for="numero_eleitoral" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('partidos.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Cadastrar partido
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
