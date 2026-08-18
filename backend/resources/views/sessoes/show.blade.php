@php
    $tipo = \App\Models\Sessao::TIPOS[$sessao->tipo] ?? ucfirst(str_replace('_', ' ', $sessao->tipo));

    $situacao = \App\Models\Sessao::SITUACOES[$sessao->situacao] ?? ucfirst(str_replace('_', ' ', $sessao->situacao));

    $variante = match ($sessao->situacao) {
        'em_preparacao' => 'warning',
        'convocada' => 'info',
        'aberta' => 'success',
        'suspensa' => 'warning',
        'cancelada' => 'danger',
        default => 'neutral',
    };

    $iconeSituacao = match ($sessao->situacao) {
        'em_preparacao' => 'fa-clock',
        'convocada' => 'fa-bullhorn',
        'aberta' => 'fa-door-open',
        'suspensa' => 'fa-pause',
        'encerrada' => 'fa-circle-check',
        'cancelada' => 'fa-circle-xmark',
        default => 'fa-circle',
    };

    $descricaoSituacao = match ($sessao->situacao) {
        'em_preparacao' => 'Aguardando convocação e numeração oficial.',
        'convocada' => 'Sessão oficialmente convocada e aguardando abertura.',
        'aberta' => 'Sessão em andamento.',
        'suspensa' => 'Sessão temporariamente suspensa.',
        'encerrada' => 'Sessão encerrada e disponível somente para consulta.',
        'cancelada' => 'Sessão cancelada e preservada no histórico.',
        default => 'Acompanhe as informações e o histórico da sessão.',
    };

    $usuarioIsRoot = auth()->user()->isRoot();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes da sessão
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert>
                    {{ session('success') }}
                </x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error">
                    {{ session('error') }}
                </x-ui::alert>
            @endif

            @error('sessao')
                <x-ui::alert type="error">
                    {{ $message }}
                </x-ui::alert>
            @enderror

            <div class="space-y-6">
                {{-- Resumo principal --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                   bg-slate-100 text-slate-500
                                   dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                        @if ($sessao->numero !== null && $sessao->ano !== null)
                                            {{ $sessao->numero }}ª Sessão {{ $tipo }}
                                        @else
                                            Sessão {{ $tipo }}
                                        @endif
                                    </h3>

                                    <x-ui::badge :variant="$variante">
                                        <i class="fa-solid {{ $iconeSituacao }} w-3 shrink-0 text-center leading-none"
                                            aria-hidden="true"></i>
                                        {{ $situacao }}
                                    </x-ui::badge>
                                </div>

                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-neutral-300">
                                    @if ($sessao->numero !== null && $sessao->ano !== null)
                                        Número oficial {{ $sessao->numero }}/{{ $sessao->ano }}.
                                    @endif

                                    {{ $descricaoSituacao }}
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                            {{-- Navegação --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <x-ui::button :href="route('sessoes.index')" variant="secondary">
                                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    Voltar
                                </x-ui::button>
                            </div>

                            {{-- Ações operacionais --}}
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                @if ($sessao->situacao === 'em_preparacao')
                                    @can('update', $sessao)
                                        <x-ui::button :href="route('sessoes.edit', $sessao)" variant="edit">
                                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                            Editar
                                        </x-ui::button>
                                    @endcan
                                @endif

                                @if ($sessao->situacao === 'em_preparacao')
                                    @can('convocar', $sessao)
                                        <form method="POST" action="{{ route('sessoes.convocar', $sessao) }}"
                                            onsubmit="return confirm('Deseja convocar esta sessão? A numeração será atribuída e a edição comum será bloqueada.');">
                                            @csrf
                                            @method('PATCH')

                                            <x-ui::button type="submit">
                                                <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                                                Convocar sessão
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endif

                                @if ($sessao->situacao === 'convocada')
                                    @can('abrir', $sessao)
                                        <form method="POST" action="{{ route('sessoes.abrir', $sessao) }}"
                                            onsubmit="return confirm('Deseja abrir esta sessão?');">
                                            @csrf
                                            @method('PATCH')

                                            <x-ui::button type="submit">
                                                <i class="fa-solid fa-door-open" aria-hidden="true"></i>
                                                Abrir sessão
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endif

                                @if ($sessao->situacao === 'suspensa')
                                    @can('retomar', $sessao)
                                        <form method="POST" action="{{ route('sessoes.retomar', $sessao) }}"
                                            onsubmit="return confirm('Deseja retomar esta sessão?');">
                                            @csrf
                                            @method('PATCH')

                                            <x-ui::button type="submit">
                                                <i class="fa-solid fa-play" aria-hidden="true"></i>
                                                Retomar sessão
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endif

                                @if ($sessao->situacao === 'aberta')
                                    @can('encerrar', $sessao)
                                        <form method="POST" action="{{ route('sessoes.encerrar', $sessao) }}"
                                            onsubmit="return confirm('Deseja encerrar esta sessão? Esta ação não poderá ser desfeita.');">
                                            @csrf
                                            @method('PATCH')

                                            <x-ui::button type="submit" variant="danger">
                                                <i class="fa-solid fa-circle-stop" aria-hidden="true"></i>
                                                Encerrar sessão
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>

                    <dl
                        class="grid gap-px border-t border-slate-200 bg-slate-200
                           sm:grid-cols-2 lg:grid-cols-4
                           dark:border-neutral-800 dark:bg-neutral-800">
                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Data prevista
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->data_hora_inicio_previsto->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Horário
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->data_hora_inicio_previsto->format('H:i') }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Local
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->local ?: 'Local a definir' }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Legislatura
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->legislatura->numero }}ª Legislatura
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Informações institucionais --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">
                    <header class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold
                               text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-circle-info text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>
                            Informações da sessão
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Dados institucionais e informações de registro.
                        </p>
                    </header>

                    <dl class="grid gap-x-8 gap-y-6 p-6 sm:grid-cols-2">
                        @if ($usuarioIsRoot)
                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-neutral-500">
                                    Câmara
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                    {{ $sessao->camara->nome }}
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                                Tipo da sessão
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $tipo }}
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                                Período da legislatura
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->legislatura->data_inicio->format('d/m/Y') }}
                                a
                                {{ $sessao->legislatura->data_fim->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                                Cadastrada por
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->criadoPor->name }}
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                                Data do cadastro
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->created_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                                Última atualização
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $sessao->updated_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Suspensão --}}
                @if ($sessao->situacao === 'aberta')
                    @can('suspender', $sessao)
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                               dark:border-neutral-800 dark:bg-neutral-900">
                            <header class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                                <h3
                                    class="flex items-center gap-2 text-base font-semibold
                                       text-slate-950 dark:text-neutral-100">
                                    <i class="fa-solid fa-pause text-slate-400 dark:text-neutral-500"
                                        aria-hidden="true"></i>
                                    Suspender sessão
                                </h3>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Informe a justificativa para interromper temporariamente a sessão.
                                </p>
                            </header>

                            <form method="POST" action="{{ route('sessoes.suspender', $sessao) }}">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-5 p-6">
                                    <div>
                                        <label for="observacao"
                                            class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                            Justificativa
                                        </label>

                                        <textarea id="observacao" name="observacao" rows="4" maxlength="2000"
                                            placeholder="Informe o motivo da suspensão." required
                                            class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                                               text-slate-950 shadow-sm placeholder:text-slate-400
                                               focus:border-indigo-500 focus:ring-indigo-500
                                               dark:border-neutral-800 dark:bg-neutral-950
                                               dark:text-neutral-100 dark:placeholder:text-neutral-600">{{ old('observacao') }}</textarea>

                                        <x-input-error for="observacao" class="mt-2 dark:text-red-400" />
                                    </div>

                                    <div class="flex justify-end">
                                        <x-ui::button type="submit" variant="danger">
                                            <i class="fa-solid fa-pause" aria-hidden="true"></i>
                                            Suspender sessão
                                        </x-ui::button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    @endcan
                @endif

                {{-- Cancelamento --}}
                @if (in_array($sessao->situacao, ['em_preparacao', 'convocada'], true))
                    @can('cancelar', $sessao)
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                               dark:border-neutral-800 dark:bg-neutral-900">
                            <header class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                                <h3
                                    class="flex items-center gap-2 text-base font-semibold
                                       text-slate-950 dark:text-neutral-100">
                                    <i class="fa-solid fa-ban text-slate-400 dark:text-neutral-500"
                                        aria-hidden="true"></i>
                                    Cancelar sessão
                                </h3>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    O cancelamento será definitivo e permanecerá registrado no histórico.
                                </p>
                            </header>

                            <form method="POST" action="{{ route('sessoes.cancelar', $sessao) }}"
                                onsubmit="return confirm('Deseja cancelar esta sessão? Esta ação não poderá ser desfeita.');">
                                @csrf
                                @method('PATCH')

                                <div class="space-y-5 p-6">
                                    <div>
                                        <label for="observacao_cancelamento"
                                            class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                            Justificativa
                                        </label>

                                        <textarea id="observacao_cancelamento" name="observacao" rows="4" maxlength="2000"
                                            placeholder="Informe o motivo do cancelamento." required
                                            class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                                               text-slate-950 shadow-sm placeholder:text-slate-400
                                               focus:border-indigo-500 focus:ring-indigo-500
                                               dark:border-neutral-800 dark:bg-neutral-950
                                               dark:text-neutral-100 dark:placeholder:text-neutral-600">{{ old('observacao') }}</textarea>

                                        <x-input-error for="observacao" class="mt-2 dark:text-red-400" />
                                    </div>

                                    <div class="flex justify-end">
                                        <x-ui::button type="submit" variant="danger">
                                            <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                            Cancelar sessão
                                        </x-ui::button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    @endcan
                @endif

                {{-- Histórico --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
           dark:border-neutral-800 dark:bg-neutral-900">

                    <div
                        class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5
               sm:flex-row sm:items-center sm:justify-between
               dark:border-neutral-800">

                        <div>
                            <h3
                                class="flex items-center gap-2 text-base font-semibold
                       text-slate-950 dark:text-neutral-100">
                                <i class="fa-solid fa-timeline text-slate-400 dark:text-neutral-500"
                                    aria-hidden="true"></i>

                                Histórico da sessão
                            </h3>

                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Registros mais recentes primeiro.
                            </p>
                        </div>

                        <span
                            class="inline-flex self-start items-center rounded-full bg-slate-100
                   px-3 py-1.5 text-xs font-semibold text-slate-600
                   dark:bg-neutral-800 dark:text-neutral-300">

                            {{ $sessao->eventos->count() }}
                            {{ $sessao->eventos->count() === 1 ? 'evento' : 'eventos' }}
                        </span>
                    </div>

                    <div class="p-6">
                        @forelse ($sessao->eventos as $evento)
                            @php
                                $acaoEvento =
                                    \App\Models\SessaoEvento::ACOES[$evento->acao] ??
                                    ucfirst(str_replace('_', ' ', $evento->acao));

                                $situacaoAnterior =
                                    \App\Models\Sessao::SITUACOES[$evento->situacao_anterior] ??
                                    ucfirst(str_replace('_', ' ', $evento->situacao_anterior));

                                $situacaoNova =
                                    \App\Models\Sessao::SITUACOES[$evento->situacao_nova] ??
                                    ucfirst(str_replace('_', ' ', $evento->situacao_nova));

                                $varianteEvento = match ($evento->acao) {
                                    'convocar' => 'info',
                                    'abrir', 'retomar' => 'success',
                                    'suspender' => 'warning',
                                    'cancelar' => 'danger',
                                    default => 'neutral',
                                };

                                $iconeEvento = match ($evento->acao) {
                                    'convocar' => 'fa-bullhorn',
                                    'abrir' => 'fa-door-open',
                                    'suspender' => 'fa-pause',
                                    'retomar' => 'fa-play',
                                    'encerrar' => 'fa-circle-stop',
                                    'cancelar' => 'fa-ban',
                                    default => 'fa-circle',
                                };

                                $iconeSituacaoEvento = match ($evento->situacao_nova) {
                                    'em_preparacao' => 'fa-clock',
                                    'convocada' => 'fa-bullhorn',
                                    'aberta' => 'fa-door-open',
                                    'suspensa' => 'fa-pause',
                                    'encerrada' => 'fa-circle-check',
                                    'cancelada' => 'fa-circle-xmark',
                                    default => 'fa-circle',
                                };
                            @endphp

                            <article class="relative pb-8 pl-10 last:pb-0">
                                @if (!$loop->last)
                                    <span
                                        class="absolute left-[0.6875rem] top-7
                               h-[calc(100%-0.5rem)] w-px
                               bg-slate-200 dark:bg-neutral-700"
                                        aria-hidden="true"></span>
                                @endif

                                <span @class([
                                    'absolute left-0 top-0 flex h-6 w-6 items-center justify-center',
                                    'rounded-full ring-4 ring-white dark:ring-neutral-900',
                                    'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300' =>
                                        $evento->acao === 'convocar',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' => in_array(
                                        $evento->acao,
                                        ['abrir', 'retomar'],
                                        true),
                                    'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' =>
                                        $evento->acao === 'suspender',
                                    'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' =>
                                        $evento->acao === 'cancelar',
                                    'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' =>
                                        $evento->acao === 'encerrar',
                                ])>
                                    <i class="fa-solid {{ $iconeEvento }} text-xs" aria-hidden="true"></i>
                                </span>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-neutral-800">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p
                                                class="flex flex-wrap items-center gap-2 text-sm font-semibold
                                       text-slate-950 dark:text-neutral-100">

                                                <span>{{ $situacaoAnterior }}</span>

                                                <i class="fa-solid fa-arrow-right-long text-xs text-slate-400
                                           dark:text-neutral-500"
                                                    aria-hidden="true"></i>

                                                <span>{{ $situacaoNova }}</span>
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                                {{ $acaoEvento }} em
                                                {{ $evento->created_at->format('d/m/Y \à\s H:i') }}
                                                por {{ $evento->executadoPor->name }}
                                            </p>
                                        </div>

                                        <x-ui::badge :variant="$varianteEvento">
                                            <i class="fa-solid {{ $iconeSituacaoEvento }} w-3 shrink-0 text-center leading-none"
                                                aria-hidden="true"></i>
                                            {{ $situacaoNova }}
                                        </x-ui::badge>
                                    </div>

                                    @if ($evento->observacao)
                                        <div class="mt-4 rounded-lg bg-slate-50 px-4 py-3 dark:bg-neutral-950">
                                            <p
                                                class="text-xs font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-neutral-400">
                                                Observação
                                            </p>

                                            <p
                                                class="mt-1 whitespace-pre-line text-sm leading-6
                                       text-slate-700 dark:text-neutral-300">
                                                {{ $evento->observacao }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="py-8 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center
                           rounded-full bg-slate-100 text-slate-400
                           dark:bg-neutral-800 dark:text-neutral-500">

                                    <i class="fa-solid fa-timeline" aria-hidden="true"></i>
                                </div>

                                <p class="mt-4 text-sm font-medium text-slate-700 dark:text-neutral-300">
                                    Nenhum evento registrado
                                </p>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    As alterações de situação da sessão aparecerão aqui.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
