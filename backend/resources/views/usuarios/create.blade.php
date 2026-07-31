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

                            @error('name')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="email" value="E-mail" />

                            <x-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="password" value="Senha inicial" />

                            <x-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required />

                            @error('password')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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
                            Defina a Câmara e o pacote inicial de permissões.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2">
                        <div>
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

                            @error('role_id')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <input type="hidden" name="ativo" value="0">

                            <label class="inline-flex items-center gap-3">
                                <input type="checkbox" name="ativo" value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked(old('ativo', '1') == '1')>

                                <span class="text-sm font-medium text-gray-700">
                                    Usuário ativo
                                </span>
                            </label>

                            @error('ativo')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
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

                            @error('permissoes')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            @error('permissoes.*')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        @endforeach
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
