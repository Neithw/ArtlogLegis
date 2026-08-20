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
                <i class="fa-solid fa-timeline text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>

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
                    \App\Models\SessaoEvento::ACOES[$evento->acao] ?? ucfirst(str_replace('_', ' ', $evento->acao));

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

                            {{-- blade-formatter-disable-next-line --}}
                                            <p class="mt-1 whitespace-pre-line text-sm leading-6
                            text-slate-700 dark:text-neutral-300">{{ $evento->observacao }}</p>
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
