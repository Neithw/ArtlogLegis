<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Novo usuário
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-6" x-data="formularioUsuario({
                pacotes: @js($pacotesPermissoes),
                papelInicial: @js(old('role_id', '')),
                permissoesIniciais: @js(old('permissoes', [])),
            })">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados de acesso
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados utilizados pelo usuário para acessar o sistema.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div>
                            <x-label for="name" value="Nome" />

                            <x-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />

                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="email" value="E-mail" />

                            <x-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />

                            <x-input-error for="email" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="password" value="Senha inicial" />

                            <x-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required />

                            <x-input-error for="password" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="Confirmação da senha" />

                            <x-input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full" required />
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Vinculação e papel
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Defina a Câmara, o papel e as unidades de atuação do usuário.
                        </p>
                    </div>

                    @php
                        $unidadesSelecionadas = collect(old('unidades_tramitacao', []))
                            ->map(fn($id) => (int) $id)
                            ->values();
                    @endphp

                    <div class="grid gap-6 p-6 md:grid-cols-2" x-data="{
                        camaraId: @js(old('camara_id', '')),
                        unidades: @js($unidadesTramitacao->values()),
                        unidadesSelecionadas: @js($unidadesSelecionadas),
                        unidadesDisponiveis() {
                            return this.unidades.filter(
                                unidade => Number(unidade.camara_id) === Number(this.camaraId)
                            );
                        }
                    }">
                        <div>
                            <x-label for="camara_id" value="Câmara" />

                            <select id="camara_id" name="camara_id" x-model="camaraId"
                                @change="unidadesSelecionadas = []"
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

                            <x-input-error for="camara_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="role_id" value="Papel" />

                            <select id="role_id" name="role_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                x-model="papelId" x-on:change="aplicarPacote($event.target.value)" required>
                                <option value="">
                                    Selecione um papel
                                </option>

                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->nome }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error for="role_id" class="mt-2" />
                        </div>


                        <div class="border-t border-gray-200 pt-6 md:col-span-2">
                            <div>
                                <x-label value="Unidades de tramitação" />

                                <p class="mt-1 text-sm text-gray-500">
                                    Selecione as unidades em que o usuário poderá receber e encaminhar proposições.
                                </p>
                            </div>

                            <p x-show="!camaraId" class="mt-4 text-sm text-gray-500">
                                Selecione uma Câmara para visualizar suas unidades de tramitação.
                            </p>

                            <p x-show="camaraId && unidadesDisponiveis().length === 0"
                                class="mt-4 text-sm text-gray-500">
                                A Câmara selecionada não possui unidades de tramitação disponíveis.
                            </p>

                            <div x-show="camaraId && unidadesDisponiveis().length > 0"
                                class="mt-4 grid gap-3 md:grid-cols-2">
                                <template x-for="unidade in unidadesDisponiveis()" :key="unidade.id">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40">
                                        <input type="checkbox" name="unidades_tramitacao[]" :value="unidade.id"
                                            x-model="unidadesSelecionadas"
                                            class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

                                        <span>
                                            <span class="block text-sm font-medium text-gray-900"
                                                x-text="unidade.nome"></span>

                                            <span x-show="unidade.sigla" class="mt-1 block text-xs text-gray-500"
                                                x-text="unidade.sigla"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>

                            <x-input-error for="unidades_tramitacao" class="mt-2" />
                            <x-input-error for="unidades_tramitacao.*" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Permissões
                                </h3>

                                <p class="mt-1 text-sm text-gray-500">
                                    Selecione as ações que o usuário poderá executar.
                                </p>
                            </div>
                            <span
                                class="whitespace-nowrap rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600"
                                x-text="`${permissoesSelecionadas.length} selecionada(s)`"></span>
                        </div>
                    </div>

                    <div class="space-y-6 p-6">
                        @foreach ($permissoesPorModulo as $modulo => $permissoesDoModulo)
                            <section>
                                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-500">
                                    {{ ucfirst($modulo) }}
                                </h4>

                                <div class="grid gap-3 md:grid-cols-2">
                                    @foreach ($permissoesDoModulo as $permissao)
                                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                                            <input type="checkbox" name="permissoes[]" :value="{{ $permissao->id }}"
                                                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                x-model="permissoesSelecionadas">

                                            <span>
                                                <span class="block text-sm font-medium text-gray-900">
                                                    {{ $permissao->nome }}
                                                </span>

                                                <span class="mt-1 block text-xs text-gray-500">
                                                    {{ $permissao->codigo }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach

                        <x-input-error for="permissoes" class="mt-2" />
                        <x-input-error for="permissoes.*" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('usuarios.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Cadastrar usuário
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
