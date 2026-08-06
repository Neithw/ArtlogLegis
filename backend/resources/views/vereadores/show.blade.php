<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold text-gray-900">
                Detalhes do vereador
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl bg-white shadow">
                <div class="border-b border-gray-200 px-6 py-5">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $vereador->nome }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $vereador->nome_parlamentar ?? 'Não informado' }}
                    </p>
                </div>

                <div class="space-y-6 p-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Câmara
                        </p>

                        <p class="mt-1 text-sm text-gray-900">
                            {{ $vereador->camara->nome }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Conta vinculada
                        </p>

                        @if ($vereador->user)
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $vereador->user->name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $vereador->user->email }}
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">
                                Sem conta vinculada
                            </p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">
                            Biografia institucional
                        </p>

                        <p class="mt-1 whitespace-pre-line text-sm text-gray-900">
                            {{ $vereador->biografia ?? 'Não informada' }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-200 px-6 py-4">
                    <a href="{{ route('vereadores.index') }}"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
