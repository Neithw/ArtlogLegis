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

            @error('pauta')
                <x-ui::alert type="error">
                    {{ $message }}
                </x-ui::alert>
            @enderror

            <div class="space-y-6">
                @include('sessoes.partials._resumo')
                @include('sessoes.partials._pauta')
                @include('sessoes.partials._presencas')
                @include('sessoes.partials._informacoes')
                @include('sessoes.partials._suspensao')
                @include('sessoes.partials._cancelamento')
                @include('sessoes.partials._historico')
            </div>
        </div>
    </div>
</x-app-layout>
