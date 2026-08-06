<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Editar Vereador
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('vereadores.update', $vereador) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados do vereador
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados do vereador.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div>
                            <x-label value="Câmara" />

                            <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $vereador->camara->nome }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    A Câmara não pode ser alterada após o cadastro.
                                </p>
                            </div>
                        </div>

                        <div>
                            <x-label for="user_id" value="Conta de acesso" />

                            <select id="user_id" name="user_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm disabled:bg-gray-100 disabled:text-gray-500 focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    Sem conta vinculada
                                </option>

                                @foreach ($usuariosDisponiveis as $usuario)
                                    <option value="{{ $usuario->id }}" @selected((string) old('user_id', $vereador->user_id) === (string) $usuario->id)>
                                        {{ $usuario->name }} - {{ $usuario->email }}

                                        @if (!$usuario->ativo)
                                            Conta desativada
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="user_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="nome" value="Nome completo" />

                            <x-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                                :value="old('nome', $vereador->nome)" required autofocus />

                            <x-input-error for="nome" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="nome_parlamentar" value="Nome parlamentar" />

                            <x-input id="nome_parlamentar" name="nome_parlamentar" type="text"
                                class="mt-1 block w-full" :value="old('nome_parlamentar', $vereador->nome_parlamentar)" />

                            <x-input-error for="nome_parlamentar" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="email_institucional" value="E-mail institucional" />

                            <x-input id="email_institucional" name="email_institucional" type="email"
                                class="mt-1 block w-full"
                                value="{{ old('email_institucional', $vereador->email_institucional) }}" />

                            <x-input-error for="email_institucional" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="telefone_institucional" value="Telefone institucional" />

                            <x-input id="telefone_institucional" name="telefone_institucional" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('telefone_institucional', $vereador->telefone_institucional) }}" />

                            <x-input-error for="telefone_institucional" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="biografia" value="Biografia institucional" />

                            <textarea id="biografia" name="biografia" rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('biografia', $vereador->biografia) }}</textarea>

                            <x-input-error for="biografia" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('vereadores.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Salvar alterações
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
