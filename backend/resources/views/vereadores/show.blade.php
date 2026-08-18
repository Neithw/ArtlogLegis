@php
    $hoje = now()->startOfDay();
    $usuarioIsRoot = auth()->user()->isRoot();
    $nomeExibicao = $vereador->nome_parlamentar ?: $vereador->nome;
    $mandatos = $vereador->mandatos->sortByDesc('data_inicio')->values();
    $mandatoAtual = $mandatos->first(
        fn($mandato) => $mandato->data_inicio->lte($hoje) && (!$mandato->data_fim || $mandato->data_fim->gte($hoje)),
    );

    if ($vereador->user?->trashed()) {
        [$situacaoConta, $varianteConta, $iconeConta] = ['Arquivada', 'danger', 'fa-box-archive'];
    } elseif ($vereador->user && !$vereador->user->ativo) {
        [$situacaoConta, $varianteConta, $iconeConta] = ['Desativada', 'warning', 'fa-circle-pause'];
    } elseif ($vereador->user) {
        [$situacaoConta, $varianteConta, $iconeConta] = ['Ativa', 'success', 'fa-circle-check'];
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Estrutura parlamentar</p>
            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes do vereador
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert>{{ session('success') }}</x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error">{{ session('error') }}</x-ui::alert>
            @endif

            <div class="space-y-6">
                {{-- Resumo principal --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $nomeExibicao }}
                                    </h3>

                                    @if ($mandatoAtual)
                                        <x-ui::badge variant="success">
                                            <i class="fa-solid fa-circle-check w-3 shrink-0 text-center leading-none"
                                                aria-hidden="true"></i>
                                            Em exercício
                                        </x-ui::badge>
                                    @endif
                                </div>

                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-neutral-300">
                                    <span class="block">{{ $vereador->nome }}</span>
                                    <span class="block">
                                        {{ $mandatoAtual
                                            ? 'Vereador com mandato parlamentar atualmente em andamento.'
                                            : 'Registro parlamentar preservado para consulta institucional.' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                            {{-- Navegação --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <x-ui::button :href="route('vereadores.index')" variant="secondary">
                                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar
                                </x-ui::button>

                                @if ($mandatoAtual)
                                    @can('view', $mandatoAtual)
                                        <x-ui::button :href="route('mandatos.show', $mandatoAtual)" variant="secondary">
                                            <i class="fa-solid fa-landmark" aria-hidden="true"></i> Ver mandato atual
                                        </x-ui::button>
                                    @endcan
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                @can('update', $vereador)
                                    <x-ui::button :href="route('vereadores.edit', $vereador)" variant="edit">
                                        <i class="fa-solid fa-pen" aria-hidden="true"></i> Editar
                                    </x-ui::button>
                                @endcan

                                @can('delete', $vereador)
                                    <form action="{{ route('vereadores.destroy', $vereador) }}" method="POST"
                                        onsubmit="return confirm('Deseja realmente arquivar este vereador?');">

                                        @csrf
                                        @method('DELETE')

                                        <x-ui::button type="submit" variant="danger">
                                            <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                            Arquivar
                                        </x-ui::button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <dl
                        class="grid gap-px border-t border-slate-200 bg-slate-200 sm:grid-cols-2 lg:grid-cols-4 dark:border-neutral-800 dark:bg-neutral-800">
                        @foreach ([
        'Nome parlamentar' => $vereador->nome_parlamentar ?: 'Não informado',
        'Mandato atual' => $mandatoAtual ? $mandatoAtual->legislatura->numero . 'ª Legislatura' : 'Nenhum em andamento',
        'Mandatos registrados' => $mandatos->count(),
        'Conta vinculada' => $vereador->user ? $situacaoConta : 'Não vinculada',
    ] as $rotulo => $valor)
                            <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                                    {{ $rotulo }}
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                    {{ $valor }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Informações institucionais --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-circle-info text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>
                            Informações do vereador
                        </h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">Dados institucionais e informações
                            de
                            contato.</p>
                    </div>

                    <dl class="grid gap-6 p-6 md:grid-cols-2">
                        @if ($usuarioIsRoot)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">Câmara</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $vereador->camara?->nome ?? 'Câmara indisponível' }}
                                </dd>
                            </div>
                        @endif

                        @foreach ([
        'Nome civil' => $vereador->nome,
        'E-mail institucional' => $vereador->email_institucional ?? 'Não informado',
        'Telefone institucional' => $vereador->telefone_institucional_formatado ?? 'Não informado',
    ] as $rotulo => $valor)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">{{ $rotulo }}
                                </dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $valor }}</dd>
                            </div>
                        @endforeach

                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">Conta vinculada</dt>
                            @if ($vereador->user)
                                <dd class="mt-2 flex flex-wrap items-center gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                            {{ $vereador->user->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-neutral-400">
                                            {{ $vereador->user->email }}</p>
                                    </div>
                                    <x-ui::badge :variant="$varianteConta">
                                        <i class="fa-solid {{ $iconeConta }} w-3 shrink-0 text-center leading-none"
                                            aria-hidden="true"></i>
                                        {{ $situacaoConta }}
                                    </x-ui::badge>
                                </dd>
                            @else
                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">Sem conta
                                    vinculada</dd>
                            @endif
                        </div>
                    </dl>
                </section>

                {{-- Biografia --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-address-card text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>
                            Biografia institucional
                        </h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">Apresentação e trajetória
                            institucional
                            do parlamentar.</p>
                    </div>
                    <div class="p-6">
                        {{-- blade-formatter-disable-next-line --}}
                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-neutral-300">{{ $vereador->biografia ?? 'Nenhuma biografia institucional informada.' }}</p>
                    </div>
                </section>

                {{-- Histórico de mandatos --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div
                        class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800">
                        <div>
                            <h3
                                class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                                <i class="fa-solid fa-timeline text-slate-400 dark:text-neutral-500"
                                    aria-hidden="true"></i>
                                Histórico de mandatos
                            </h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">Mandatos mais recentes
                                primeiro.
                            </p>
                        </div>
                        <span
                            class="inline-flex self-start items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-neutral-800 dark:text-neutral-300">
                            {{ $mandatos->count() }} {{ $mandatos->count() === 1 ? 'registro' : 'registros' }}
                        </span>
                    </div>

                    <div class="p-6">
                        @forelse ($mandatos as $mandato)
                            @php
                                if ($mandato->data_inicio->gt($hoje)) {
                                    [$situacaoMandato, $varianteMandato, $iconeMandato] = [
                                        'Futuro',
                                        'info',
                                        'fa-clock',
                                    ];
                                } elseif ($mandato->data_fim && $mandato->data_fim->lt($hoje)) {
                                    [$situacaoMandato, $varianteMandato, $iconeMandato] = [
                                        'Encerrado',
                                        'neutral',
                                        'fa-flag-checkered',
                                    ];
                                } else {
                                    [$situacaoMandato, $varianteMandato, $iconeMandato] = [
                                        'Em andamento',
                                        'success',
                                        'fa-circle-check',
                                    ];
                                }
                            @endphp

                            <article class="relative pb-8 pl-10 last:pb-0">
                                @if (!$loop->last)
                                    <span
                                        class="absolute left-[0.6875rem] top-7 h-[calc(100%-0.5rem)] w-px bg-slate-200 dark:bg-neutral-700"
                                        aria-hidden="true"></span>
                                @endif

                                <span @class([
                                    'absolute left-0 top-0 flex h-6 w-6 items-center justify-center rounded-full ring-4 ring-white dark:ring-neutral-900',
                                    'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300' =>
                                        $situacaoMandato === 'Futuro',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' =>
                                        $situacaoMandato === 'Em andamento',
                                    'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' =>
                                        $situacaoMandato === 'Encerrado',
                                ])>
                                    <i class="fa-solid {{ $iconeMandato }} text-xs" aria-hidden="true"></i>
                                </span>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-neutral-800">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                                {{ $mandato->legislatura->numero }}ª Legislatura
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                                De {{ $mandato->data_inicio->format('d/m/Y') }} até
                                                {{ $mandato->data_fim?->format('d/m/Y') ?? 'o presente' }}.
                                            </p>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-ui::badge :variant="$varianteMandato">
                                                <i class="fa-solid {{ $iconeMandato }} w-3 shrink-0 text-center leading-none"
                                                    aria-hidden="true"></i>
                                                {{ $situacaoMandato }}
                                            </x-ui::badge>

                                            @can('view', $mandato)
                                                <x-ui::button :href="route('mandatos.show', $mandato)" variant="secondary">
                                                    <i class="fa-solid fa-arrow-up-right-from-square"
                                                        aria-hidden="true"></i>
                                                    Ver mandato
                                                </x-ui::button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="py-8 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-neutral-800 dark:text-neutral-500">
                                    <i class="fa-solid fa-landmark" aria-hidden="true"></i>
                                </div>
                                <p class="mt-4 text-sm font-medium text-slate-700 dark:text-neutral-300">Nenhum mandato
                                    registrado</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">Os mandatos vinculados ao
                                    vereador aparecerão aqui.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
