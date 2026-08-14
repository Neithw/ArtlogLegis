<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Editar usuário
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @php
                $permissoesDisponiveis = $permissoesPorModulo->flatten(1)->keyBy('id');

                $idsPermissoesDisponiveis = $permissoesDisponiveis->keys()->map(fn($id) => (int) $id)->values();

                $camaraInicial = $usuarioIsRoot
                    ? (string) old('camara_id', $user->camara_id)
                    : (string) auth()->user()->camara_id;

                $camaraVinculada = $usuarioIsRoot ? null : $camaras->firstWhere('id', $camaraInicial);

                $rotulosModulos = [
                    'usuarios' => 'Usuários',
                    'vereadores' => 'Vereadores',
                    'legislaturas' => 'Legislaturas',
                    'partidos' => 'Partidos',
                    'mandatos' => 'Mandatos',
                    'tipos-proposicao' => 'Tipos de proposição',
                    'proposicoes' => 'Proposições',
                    'unidades-tramitacao' => 'Unidades de tramitação',
                    'tramitacoes' => 'Tramitações',
                ];
            @endphp

            <form method="POST" action="{{ route('usuarios.update', $user) }}" class="space-y-6" x-data="formularioUsuario({
                pacotes: @js($pacotesPermissoes),
                papelInicial: @js(old('role_id', $user->role_id)),
                permissoesIniciais: @js(session()->hasOldInput() ? old('permissoes', []) : $permissoesSelecionadas),
                permissoesDisponiveis: @js($idsPermissoesDisponiveis),
            })">
                @csrf
                @method('PUT')

                <x-ui::card>
                    <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-key" aria-hidden="true"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                    Dados de acesso
                                </h3>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Informe os dados utilizados pelo usuário para acessar o sistema.
                                </p>
                            </div>
                        </div>
                    </header>

                    <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                        <div>
                            <label for="name"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                Nome
                                <span class="text-red-600 dark:text-red-400" aria-hidden="true">*</span>
                            </label>

                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                required autofocus autocomplete="name" placeholder="Nome completo"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600">

                            <x-input-error for="name" class="mt-1.5 dark:text-red-400" />
                        </div>

                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                E-mail
                                <span class="text-red-600 dark:text-red-400" aria-hidden="true">*</span>
                            </label>

                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                                required autocomplete="email" placeholder="usuario@exemplo.com"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600">

                            <x-input-error for="email" class="mt-1.5 dark:text-red-400" />
                        </div>

                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                Nova senha
                            </label>

                            <input id="password" name="password" type="password" autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100">

                            <p class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
                                Deixe em branco para manter a senha atual.
                            </p>

                            <x-input-error for="password" class="mt-1.5 dark:text-red-400" />
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                Confirmação da nova senha
                            </label>

                            <input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100">

                            <x-input-error for="password_confirmation" class="mt-1.5 dark:text-red-400" />
                        </div>
                    </div>
                </x-ui::card>

                <x-ui::card>
                    <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-sitemap" aria-hidden="true"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                    Vinculação e atuação
                                </h3>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Defina a Câmara e as unidades de atuação do usuário.
                                </p>
                            </div>
                        </div>
                    </header>

                    @php
                        $unidadesSelecionadas = collect(
                            session()->hasOldInput() ? old('unidades_tramitacao', []) : $unidadesTramitacaoSelecionadas,
                        )
                            ->map(fn($id) => (int) $id)
                            ->values();
                    @endphp

                    <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2" x-data="{
                        camaraId: @js($camaraInicial),
                        unidades: @js($unidadesTramitacao->values()),
                        unidadesSelecionadas: @js($unidadesSelecionadas),
                        unidadesDisponiveis() {
                            return this.unidades.filter(
                                unidade => Number(unidade.camara_id) === Number(this.camaraId)
                            );
                        }
                    }">
                        <div class="md:col-span-2">
                            <label for="camara_id"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                Câmara
                                <span class="text-red-600 dark:text-red-400" aria-hidden="true">*</span>
                            </label>

                            @if ($usuarioIsRoot)
                                <select id="camara_id" name="camara_id" x-model="camaraId"
                                    @change="unidadesSelecionadas = []" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100">
                                    <option value="">Selecione uma Câmara</option>

                                    @foreach ($camaras as $camara)
                                        <option value="{{ $camara->id }}" @selected(old('camara_id', $user->camara_id) == $camara->id)>
                                            {{ $camara->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" id="camara_id" name="camara_id" value="{{ $camaraInicial }}"
                                    x-model="camaraId">

                                <div
                                    class="mt-1 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 dark:border-neutral-800 dark:bg-neutral-950/50">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm dark:bg-neutral-900 dark:text-neutral-400">
                                        <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                            {{ $camaraVinculada?->nome ?? 'Câmara indisponível' }}
                                        </p>

                                        <p class="text-xs text-slate-500 dark:text-neutral-500">
                                            Vinculação definida pela sua conta.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <x-input-error for="camara_id" class="mt-1.5 dark:text-red-400" />
                        </div>

                        <section class="border-t border-slate-200 pt-6 md:col-span-2 dark:border-neutral-800">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    Unidades de tramitação
                                </h4>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Selecione as unidades em que o usuário poderá receber e encaminhar proposições.
                                </p>
                            </div>

                            <div x-cloak x-show="!camaraId"
                                class="mt-4 flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-neutral-800 dark:bg-neutral-950/50 dark:text-neutral-400">
                                <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                                <span>Selecione uma Câmara para visualizar suas unidades de tramitação.</span>
                            </div>

                            <div x-cloak x-show="camaraId && unidadesDisponiveis().length === 0"
                                class="mt-4 flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-neutral-800 dark:bg-neutral-950/50 dark:text-neutral-400">
                                <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                                <span>A Câmara selecionada não possui unidades de tramitação disponíveis.</span>
                            </div>

                            <div x-cloak x-show="camaraId && unidadesDisponiveis().length > 0"
                                class="mt-4 grid gap-3 md:grid-cols-2">
                                <template x-for="unidade in unidadesDisponiveis()" :key="unidade.id">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40 dark:border-neutral-800 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/20">
                                        <input type="checkbox" name="unidades_tramitacao[]" :value="unidade.id"
                                            x-model="unidadesSelecionadas"
                                            class="mt-0.5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:checked:bg-indigo-600">

                                        <span class="min-w-0">
                                            <span
                                                class="block text-sm font-medium text-slate-950 dark:text-neutral-100"
                                                x-text="unidade.nome"></span>

                                            <span x-show="unidade.sigla"
                                                class="mt-1 block text-xs text-slate-500 dark:text-neutral-500"
                                                x-text="unidade.sigla"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>

                            <x-input-error for="unidades_tramitacao" class="mt-1.5 dark:text-red-400" />
                            <x-input-error for="unidades_tramitacao.*" class="mt-1.5 dark:text-red-400" />
                        </section>
                    </div>
                </x-ui::card>

                <x-ui::card>
                    <header
                        class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5 sm:flex-row sm:items-start sm:justify-between sm:px-6 dark:border-neutral-800">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                    Controle de acesso
                                </h3>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Escolha um pacote de acesso e, se necessário, personalize suas permissões.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span x-cloak x-show="possuiPersonalizacao"
                                class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950/50 dark:text-amber-300">
                                Personalizado
                            </span>

                            <span
                                class="inline-flex w-fit items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-neutral-800 dark:text-neutral-300"
                                x-text="`${permissoesSelecionadas.length} selecionada(s)`"></span>
                        </div>
                    </header>

                    <div class="space-y-6 p-4 sm:p-6">
                        <section>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    Pacote de acesso
                                    <span class="text-red-600 dark:text-red-400" aria-hidden="true">*</span>
                                </h4>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    O pacote define o papel do usuário e aplica uma seleção inicial de permissões.
                                </p>
                            </div>

                            <div class="mt-4 grid items-start gap-4 md:grid-cols-2">
                                @foreach ($roles as $role)
                                    @php
                                        $descricaoPapel = match ($role->codigo) {
                                            'gerente' => 'Administração dos usuários e dos módulos da Câmara.',
                                            'usuario_comum' => 'Acesso às rotinas operacionais e de consulta.',
                                            default => 'Conjunto de acessos definido para este papel.',
                                        };

                                        $iconePapel = match ($role->codigo) {
                                            'gerente' => 'fa-user-gear',
                                            'usuario_comum' => 'fa-user',
                                            default => 'fa-shield-halved',
                                        };

                                        $idsDoPacote = collect($pacotesPermissoes[$role->id] ?? [])->map(
                                            fn($id) => (int) $id,
                                        );

                                        $permissoesDoPacote = $idsDoPacote
                                            ->map(fn($id) => $permissoesDisponiveis->get($id))
                                            ->filter();
                                    @endphp

                                    <div class="overflow-hidden rounded-xl border transition"
                                        :class="papelId === '{{ $role->id }}'
                                            ?
                                            'border-indigo-500 ring-2 ring-indigo-500/20 dark:border-indigo-400' :
                                            'border-slate-200 hover:border-indigo-300 dark:border-neutral-800 dark:hover:border-indigo-800'">
                                        <label class="flex cursor-pointer items-start gap-4 p-4">
                                            <input type="radio" name="role_id" value="{{ $role->id }}"
                                                x-model="papelId" x-on:change="selecionarPapel('{{ $role->id }}')"
                                                class="sr-only" required>

                                            <span
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400">
                                                <i class="fa-solid {{ $iconePapel }}" aria-hidden="true"></i>
                                            </span>

                                            <span class="min-w-0 flex-1">
                                                <span class="flex items-start justify-between gap-3">
                                                    <span class="font-semibold text-slate-950 dark:text-neutral-100">
                                                        {{ $role->nome }}
                                                    </span>

                                                    <i x-cloak x-show="papelId === '{{ $role->id }}'"
                                                        class="fa-solid fa-circle-check text-indigo-600 dark:text-indigo-400"
                                                        aria-hidden="true"></i>
                                                </span>

                                                <span class="mt-1 block text-sm text-slate-500 dark:text-neutral-400">
                                                    {{ $descricaoPapel }}
                                                </span>

                                                <span
                                                    class="mt-3 block text-xs font-medium text-slate-500 dark:text-neutral-500">
                                                    {{ $permissoesDoPacote->count() }} permissões incluídas
                                                </span>
                                            </span>
                                        </label>

                                        <details class="border-t border-slate-200 dark:border-neutral-800">
                                            <summary
                                                class="cursor-pointer px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-800/50">
                                                Ver permissões do pacote
                                            </summary>

                                            <ul
                                                class="max-h-56 space-y-2 overflow-y-auto border-t border-slate-200 px-4 py-3 text-sm text-slate-600 dark:border-neutral-800 dark:text-neutral-400">
                                                @forelse ($permissoesDoPacote as $permissao)
                                                    <li class="flex items-start gap-2">
                                                        <i class="fa-solid fa-check mt-1 text-emerald-600 dark:text-emerald-400"
                                                            aria-hidden="true"></i>
                                                        <span>{{ $permissao->nome }}</span>
                                                    </li>
                                                @empty
                                                    <li>Nenhuma permissão disponível neste pacote.</li>
                                                @endforelse
                                            </ul>
                                        </details>
                                    </div>
                                @endforeach
                            </div>

                            <x-input-error for="role_id" class="mt-1.5 dark:text-red-400" />
                        </section>

                        <section class="border-t border-slate-200 pt-6 dark:border-neutral-800">
                            <button type="button" x-on:click="personalizacaoAberta = !personalizacaoAberta"
                                class="flex w-full items-center justify-between gap-4 rounded-lg text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-4 dark:focus:ring-offset-neutral-900"
                                :aria-expanded="personalizacaoAberta">
                                <span>
                                    <span class="block text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                        Personalizar permissões
                                    </span>

                                    <span class="mt-1 block text-sm text-slate-500 dark:text-neutral-400">
                                        Ajuste individualmente os acessos aplicados pelo pacote selecionado.
                                    </span>
                                </span>

                                <i class="fa-solid fa-chevron-down text-slate-400 transition-transform dark:text-neutral-500"
                                    :class="personalizacaoAberta && 'rotate-180'" aria-hidden="true"></i>
                            </button>

                            <div x-cloak x-show="personalizacaoAberta" class="mt-6 space-y-8">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <button type="button" x-on:click="limparPermissoes()"
                                        class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-neutral-100">
                                        Limpar tudo
                                    </button>

                                    <button type="button" x-on:click="selecionarTodasPermissoes()"
                                        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900">
                                        Selecionar tudo
                                    </button>
                                </div>

                                @foreach ($permissoesPorModulo as $modulo => $permissoesDoModulo)
                                    <section>
                                        <h5
                                            class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-neutral-500">
                                            {{ $rotulosModulos[$modulo] ?? ucfirst(str_replace(['-', '_'], ' ', $modulo)) }}
                                        </h5>

                                        <div class="grid gap-3 md:grid-cols-2">
                                            @foreach ($permissoesDoModulo as $permissao)
                                                <label
                                                    class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40 dark:border-neutral-800 dark:hover:border-indigo-800 dark:hover:bg-indigo-950/20">
                                                    <input type="checkbox" name="permissoes[]"
                                                        :value="{{ $permissao->id }}" x-model="permissoesSelecionadas"
                                                        class="mt-0.5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:checked:bg-indigo-600">

                                                    <span
                                                        class="text-sm font-medium text-slate-950 dark:text-neutral-100">
                                                        {{ $permissao->nome }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>

                            <x-input-error for="permissoes" class="mt-1.5 dark:text-red-400" />
                            <x-input-error for="permissoes.*" class="mt-1.5 dark:text-red-400" />
                        </section>
                    </div>
                </x-ui::card>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <x-ui::button :href="route('usuarios.index')" variant="secondary">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Cancelar
                    </x-ui::button>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                        Atualizar usuário
                    </x-ui::button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
