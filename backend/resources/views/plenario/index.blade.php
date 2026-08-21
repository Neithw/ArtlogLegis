@php
    $nomeParlamentar = $vereador->nome_parlamentar ?: $vereador->nome;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Plenário digital
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Sessões disponíveis
            </h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
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

            <section
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-5
                       shadow-sm sm:p-6
                       dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center
                               rounded-full bg-indigo-50 text-indigo-600
                               dark:bg-indigo-950 dark:text-indigo-300">
                        <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500 dark:text-neutral-400">
                            Parlamentar autenticado
                        </p>

                        <h3 class="font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $nomeParlamentar }}
                        </h3>
                    </div>
                </div>

                <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-neutral-400">
                    Selecione uma sessão para confirmar sua presença,
                    acompanhar a pauta e participar das votações.
                </p>
            </section>

            <div class="space-y-4">
                @forelse ($sessoes as $sessao)
                    @php
                        $tipo =
                            \App\Models\Sessao::TIPOS[$sessao->tipo] ?? ucfirst(str_replace('_', ' ', $sessao->tipo));

                        $situacao =
                            \App\Models\Sessao::SITUACOES[$sessao->situacao] ??
                            ucfirst(str_replace('_', ' ', $sessao->situacao));

                        $titulo = $sessao->numero ? "{$sessao->numero}ª Sessão {$tipo}" : "Sessão {$tipo}";

                        $sessaoAtiva = in_array($sessao->situacao, ['convocada', 'aberta', 'suspensa'], true);
                    @endphp

                    <article @class([
                        'overflow-hidden rounded-2xl border bg-white shadow-sm',
                        'dark:bg-neutral-900',
                        'border-emerald-300 dark:border-emerald-900' =>
                            $sessao->situacao === 'aberta',
                        'border-slate-200 dark:border-neutral-800' =>
                            $sessao->situacao !== 'aberta',
                    ])>
                        <div class="p-5 sm:p-6">
                            <div
                                class="flex flex-col gap-4
                                       sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-3">
                                    <div @class([
                                        'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl',
                                        'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300' =>
                                            $sessao->situacao === 'aberta',
                                        'bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300' =>
                                            $sessao->situacao !== 'aberta',
                                    ])>
                                        <i class="fa-solid fa-landmark" aria-hidden="true"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm text-slate-500 dark:text-neutral-400">
                                            {{ $sessao->camara->nome }}
                                        </p>

                                        <h3
                                            class="mt-0.5 text-lg font-semibold
                                                   text-slate-950 dark:text-neutral-100">
                                            {{ $titulo }}
                                        </h3>
                                    </div>
                                </div>

                                @if ($sessao->situacao === 'aberta')
                                    <span
                                        class="inline-flex self-start items-center gap-2 rounded-full
                                               bg-emerald-50 px-3 py-1.5 text-xs font-semibold
                                               text-emerald-700
                                               dark:bg-emerald-950 dark:text-emerald-300">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        {{ $situacao }}
                                    </span>
                                @elseif ($sessao->situacao === 'suspensa')
                                    <span
                                        class="inline-flex self-start items-center gap-2 rounded-full
                                               bg-amber-50 px-3 py-1.5 text-xs font-semibold
                                               text-amber-700
                                               dark:bg-amber-950 dark:text-amber-300">
                                        <i class="fa-solid fa-pause" aria-hidden="true"></i>
                                        {{ $situacao }}
                                    </span>
                                @elseif ($sessao->situacao === 'convocada')
                                    <span
                                        class="inline-flex self-start items-center gap-2 rounded-full
                                               bg-blue-50 px-3 py-1.5 text-xs font-semibold
                                               text-blue-700
                                               dark:bg-blue-950 dark:text-blue-300">
                                        <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                                        {{ $situacao }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex self-start items-center gap-2 rounded-full
                                               bg-slate-100 px-3 py-1.5 text-xs font-semibold
                                               text-slate-600
                                               dark:bg-neutral-800 dark:text-neutral-300">
                                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                        {{ $situacao }}
                                    </span>
                                @endif
                            </div>

                            <dl class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <dt
                                        class="text-xs font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-neutral-500">
                                        Data e horário
                                    </dt>

                                    <dd
                                        class="mt-1 text-sm font-medium
                                               text-slate-950 dark:text-neutral-100">
                                        {{ $sessao->data_hora_inicio_previsto->format('d/m/Y \à\s H:i') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt
                                        class="text-xs font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-neutral-500">
                                        Local
                                    </dt>

                                    <dd
                                        class="mt-1 text-sm font-medium
                                               text-slate-950 dark:text-neutral-100">
                                        {{ $sessao->local ?: 'Local a definir' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt
                                        class="text-xs font-semibold uppercase tracking-wide
                                               text-slate-500 dark:text-neutral-500">
                                        Legislatura
                                    </dt>

                                    <dd
                                        class="mt-1 text-sm font-medium
                                               text-slate-950 dark:text-neutral-100">
                                        {{ $sessao->legislatura->numero }}ª Legislatura
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-5 flex justify-end">
                                <a href="{{ route('plenario.sessoes.show', $sessao) }}"
                                    class="inline-flex w-full items-center justify-center gap-2
                                           rounded-xl bg-indigo-600 px-5 py-3 text-sm
                                           font-semibold text-white transition
                                           hover:bg-indigo-700 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-500
                                           focus:ring-offset-2 sm:w-auto
                                           dark:focus:ring-offset-neutral-900">
                                    <i class="fa-solid {{ $sessaoAtiva ? 'fa-door-open' : 'fa-eye' }}"
                                        aria-hidden="true"></i>

                                    {{ $sessaoAtiva ? 'Entrar no plenário' : 'Consultar sessão' }}
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <section
                        class="rounded-2xl border border-dashed border-slate-300
                               bg-white p-8 text-center shadow-sm
                               dark:border-neutral-700 dark:bg-neutral-900">
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center
                                   rounded-full bg-slate-100 text-slate-400
                                   dark:bg-neutral-800 dark:text-neutral-500">
                            <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>
                        </div>

                        <h3
                            class="mt-4 font-semibold text-slate-950
                                   dark:text-neutral-100">
                            Nenhuma sessão disponível
                        </h3>

                        <p
                            class="mx-auto mt-2 max-w-md text-sm leading-6
                                   text-slate-600 dark:text-neutral-400">
                            Não existem sessões disponíveis para seu mandato neste momento.
                        </p>
                    </section>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
