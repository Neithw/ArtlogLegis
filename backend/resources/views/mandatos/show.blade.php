@php
    $hoje = now()->startOfDay();
    $usuarioIsRoot = auth()->user()->isRoot();

    if ($hoje->lt($mandato->data_inicio)) {
        $situacao = 'Futuro';
        $variante = 'info';
        $iconeSituacao = 'fa-clock';
        $descricaoSituacao = 'Mandato programado para iniciar futuramente.';
    } elseif ($mandato->data_fim && $hoje->gt($mandato->data_fim)) {
        $situacao = 'Encerrado';
        $variante = 'neutral';
        $iconeSituacao = 'fa-flag-checkered';
        $descricaoSituacao = 'Mandato encerrado e preservado para consulta.';
    } else {
        $situacao = 'Em andamento';
        $variante = 'success';
        $iconeSituacao = 'fa-circle-check';
        $descricaoSituacao = 'Mandato parlamentar atualmente em exercício.';
    }

    $filiacoes = $mandato->filiacoesPartidarias->sortBy('data_inicio')->values();

    $filiacoesRecentes = $filiacoes->sortByDesc('data_inicio')->values();

    $filiacaoVigente = $filiacoes->first(
        fn($filiacao) => $filiacao->data_inicio->lte($hoje) &&
            (!$filiacao->data_fim || $filiacao->data_fim->gte($hoje)),
    );

    if ($situacao === 'Futuro') {
        $rotuloPartido = 'Partido inicial';
        $filiacaoDestaque = $filiacoes->first();
    } elseif ($situacao === 'Encerrado') {
        $rotuloPartido = 'Último partido';
        $filiacaoDestaque = $filiacoes->last();
    } else {
        $rotuloPartido = 'Partido atual';
        $filiacaoDestaque = $filiacaoVigente;
    }

    $nomeVereador = $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes do mandato
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

            @error('mandato')
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
                                <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                        Mandato de {{ $nomeVereador }}
                                    </h3>

                                    <x-ui::badge :variant="$variante">
                                        <i class="fa-solid {{ $iconeSituacao }} w-3 shrink-0
                                               text-center leading-none"
                                            aria-hidden="true"></i>

                                        {{ $situacao }}
                                    </x-ui::badge>
                                </div>

                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-neutral-300">

                                    <span class="block">
                                        Exercício parlamentar na
                                        {{ $mandato->legislatura->numero }}ª Legislatura.
                                    </span>

                                    <span class="block">
                                        {{ $descricaoSituacao }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                            {{-- Navegação --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <x-ui::button :href="route('mandatos.index')" variant="secondary">

                                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    Voltar
                                </x-ui::button>

                                @can('view', $mandato->vereador)
                                    <x-ui::button :href="route('vereadores.show', $mandato->vereador)" variant="secondary">

                                        <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                                        Ver vereador
                                    </x-ui::button>
                                @endcan
                            </div>

                            {{-- Ações operacionais --}}
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                @can('update', $mandato)
                                    <x-ui::button :href="route('mandatos.edit', $mandato)" variant="edit">

                                        <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        Editar
                                    </x-ui::button>
                                @endcan

                                @canany(['update', 'delete'], $mandato)
                                    @can('update', $mandato)
                                        <x-ui::button :href="route('mandatos.troca-partidaria.create', $mandato)">

                                            <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                                            Trocar partido
                                        </x-ui::button>
                                    @endcan

                                    @can('delete', $mandato)
                                        <form action="{{ route('mandatos.destroy', $mandato) }}" method="POST"
                                            onsubmit="return confirm('Deseja realmente arquivar este mandato?');">

                                            @csrf
                                            @method('DELETE')

                                            <x-ui::button type="submit" variant="danger">
                                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                                Arquivar
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endcanany
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
                                Início do mandato
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $mandato->data_inicio->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Término do mandato
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $mandato->data_fim?->format('d/m/Y') ?? 'Em aberto' }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Legislatura
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $mandato->legislatura->numero }}ª Legislatura
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                {{ $rotuloPartido }}
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $filiacaoDestaque?->partido?->sigla ?? 'Não informado' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Informações institucionais --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold
                               text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-circle-info text-slate-400
                                   dark:text-neutral-500"
                                aria-hidden="true"></i>

                            Informações do mandato
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Dados institucionais e informações de registro.
                        </p>
                    </div>

                    <dl class="grid gap-6 p-6 md:grid-cols-2">
                        @if ($usuarioIsRoot)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                    Câmara
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $mandato->legislatura->camara->nome }}
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Vereador
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->vereador->nome }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Nome parlamentar
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->vereador->nome_parlamentar ?: 'Não informado' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Período da legislatura
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->legislatura->data_inicio->format('d/m/Y') }}
                                a
                                {{ $mandato->legislatura->data_fim->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Filiações registradas
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $filiacoes->count() }}
                                {{ $filiacoes->count() === 1 ? 'filiação' : 'filiações' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Data do cadastro
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->created_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Última atualização
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->updated_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Histórico partidário --}}
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
                                <i class="fa-solid fa-timeline text-slate-400
                                       dark:text-neutral-500"
                                    aria-hidden="true"></i>

                                Histórico partidário
                            </h3>

                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Filiações mais recentes primeiro.
                            </p>
                        </div>

                        <span
                            class="inline-flex self-start items-center rounded-full bg-slate-100
                               px-3 py-1.5 text-xs font-semibold text-slate-600
                               dark:bg-neutral-800 dark:text-neutral-300">

                            {{ $filiacoes->count() }}
                            {{ $filiacoes->count() === 1 ? 'registro' : 'registros' }}
                        </span>
                    </div>

                    <div class="p-6">
                        @forelse ($filiacoesRecentes as $filiacao)
                            @php
                                if ($filiacao->data_inicio->gt($hoje)) {
                                    $situacaoFiliacao = 'Futura';
                                    $varianteFiliacao = 'info';
                                    $iconeFiliacao = 'fa-clock';
                                } elseif (!$filiacao->data_fim || $filiacao->data_fim->gte($hoje)) {
                                    $situacaoFiliacao = 'Vigente';
                                    $varianteFiliacao = 'success';
                                    $iconeFiliacao = 'fa-circle-check';
                                } else {
                                    $situacaoFiliacao = 'Encerrada';
                                    $varianteFiliacao = 'neutral';
                                    $iconeFiliacao = 'fa-flag-checkered';
                                }
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
                                        $situacaoFiliacao === 'Futura',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' =>
                                        $situacaoFiliacao === 'Vigente',
                                    'bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-neutral-300' =>
                                        $situacaoFiliacao === 'Encerrada',
                                ])>
                                    <i class="fa-solid {{ $iconeFiliacao }} text-xs" aria-hidden="true"></i>
                                </span>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-neutral-800">
                                    <div
                                        class="flex flex-col gap-3
                                           sm:flex-row sm:items-start sm:justify-between">

                                        <div>
                                            <p
                                                class="flex flex-wrap items-center gap-2 text-sm font-semibold
                                                   text-slate-950 dark:text-neutral-100">
                                                <span>{{ $filiacao->partido->sigla }}</span>

                                                <span class="text-slate-300 dark:text-neutral-700" aria-hidden="true">
                                                    —
                                                </span>

                                                <span>{{ $filiacao->partido->nome }}</span>
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                                Filiação iniciada em
                                                {{ $filiacao->data_inicio->format('d/m/Y') }}.
                                            </p>
                                        </div>

                                        <x-ui::badge :variant="$varianteFiliacao">
                                            <i class="fa-solid {{ $iconeFiliacao }} w-3 shrink-0
                                                   text-center leading-none"
                                                aria-hidden="true"></i>

                                            {{ $situacaoFiliacao }}
                                        </x-ui::badge>
                                    </div>

                                    @if ($filiacao->data_fim)
                                        @php
                                            $terminoFuturo = $filiacao->data_fim->gte($hoje);
                                        @endphp

                                        <p
                                            class="mt-4 flex items-center gap-2 text-xs
                                               text-slate-500 dark:text-neutral-400">
                                            <i class="fa-solid fa-calendar-check
                                                   text-slate-400 dark:text-neutral-500"
                                                aria-hidden="true"></i>

                                            {{ $terminoFuturo ? 'Término previsto para' : 'Encerrada em' }}
                                            {{ $filiacao->data_fim->format('d/m/Y') }}.
                                        </p>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="py-8 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center
                                       rounded-full bg-slate-100 text-slate-400
                                       dark:bg-neutral-800 dark:text-neutral-500">
                                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                                </div>

                                <p class="mt-4 text-sm font-medium text-slate-700 dark:text-neutral-300">
                                    Nenhuma filiação partidária registrada
                                </p>

                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    As filiações vinculadas ao mandato aparecerão aqui.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
