@if ($votacoesFinalizadas->isNotEmpty())
    <section
        class="overflow-hidden rounded-2xl border border-slate-200
               bg-white shadow-sm
               dark:border-neutral-800 dark:bg-neutral-900">
        <header
            class="flex items-center justify-between gap-3
                   border-b border-slate-200 px-5 py-4
                   dark:border-neutral-800">
            <h3
                class="flex items-center gap-2 font-semibold
                       text-slate-950 dark:text-neutral-100">
                <i class="fa-solid fa-clock-rotate-left
                          text-slate-400 dark:text-neutral-500"
                    aria-hidden="true"></i>

                Histórico de votações
            </h3>

            <span
                class="rounded-full bg-slate-100 px-3 py-1
                       text-xs font-semibold text-slate-600
                       dark:bg-neutral-800 dark:text-neutral-300">
                {{ $votacoesFinalizadas->count() }}
                {{ $votacoesFinalizadas->count() === 1 ? 'registro' : 'registros' }}
            </span>
        </header>

        <div class="divide-y divide-slate-200 dark:divide-neutral-800">
            @foreach ($votacoesFinalizadas as $votacaoFinalizada)
                @php
                    $proposicao = $votacaoFinalizada->itemPauta->proposicao;

                    $cancelada = $votacaoFinalizada->situacao === 'cancelada';

                    $rotuloResultado = $cancelada
                        ? 'Cancelada'
                        : \App\Models\Votacao::RESULTADOS[$votacaoFinalizada->resultado] ?? 'Sem resultado';

                    $varianteResultado = $cancelada
                        ? 'danger'
                        : match ($votacaoFinalizada->resultado) {
                            'aprovada' => 'success',
                            'rejeitada' => 'danger',
                            'empate' => 'warning',
                            'sem_decisao' => 'neutral',
                            default => 'neutral',
                        };

                    $favoraveis = $votacaoFinalizada->votos->where('escolha', 'favoravel')->count();

                    $contrarios = $votacaoFinalizada->votos->where('escolha', 'contrario')->count();

                    $abstencoes = $votacaoFinalizada->votos->where('escolha', 'abstencao')->count();

                    $meuVotoFinalizado = $votacaoFinalizada->votos->firstWhere('mandato_id', $mandato->id);

                    $rotuloMeuVoto = $meuVotoFinalizado
                        ? \App\Models\Voto::ESCOLHAS[$meuVotoFinalizado->escolha] ?? $meuVotoFinalizado->escolha
                        : null;

                    $configuracaoMeuVoto = match ($meuVotoFinalizado?->escolha) {
                        'favoravel' => [
                            'variante' => 'success',
                            'icone' => 'fa-thumbs-up',
                        ],

                        'contrario' => [
                            'variante' => 'danger',
                            'icone' => 'fa-thumbs-down',
                        ],

                        'abstencao' => [
                            'variante' => 'neutral',
                            'icone' => 'fa-minus',
                        ],

                        default => [
                            'variante' => 'neutral',
                            'icone' => 'fa-check',
                        ],
                    };

                    $dataFinalizacao = $cancelada ? $votacaoFinalizada->cancelada_em : $votacaoFinalizada->encerrada_em;
                @endphp

                <details class="group">
                    <summary
                        class="flex cursor-pointer list-none items-start
                               justify-between gap-4 px-5 py-4 transition-colors
                               hover:bg-slate-50
                               focus:outline-none focus-visible:ring-2
                               focus-visible:ring-inset focus-visible:ring-indigo-500
                               dark:hover:bg-neutral-800/40
                               sm:items-center sm:px-6
                               [&::-webkit-details-marker]:hidden">
                        <div class="min-w-0">
                            <p
                                class="text-xs font-medium
                                       text-indigo-600 dark:text-indigo-400">
                                Item {{ $votacaoFinalizada->itemPauta->ordem }} da pauta
                            </p>

                            <h4
                                class="mt-1 font-semibold text-slate-950
                                       dark:text-neutral-100">
                                {{ $proposicao->tipoProposicao->nome }}
                                nº {{ $proposicao->numero }}/{{ $proposicao->ano }}
                            </h4>

                            <p
                                class="mt-1 text-xs
                                       text-slate-500 dark:text-neutral-400">
                                {{ $cancelada ? 'Cancelada' : 'Finalizada' }}
                                em
                                {{ $dataFinalizacao?->format('d/m/Y \à\s H:i') ?? 'data não informada' }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <x-ui::badge :variant="$varianteResultado">
                                {{ $rotuloResultado }}
                            </x-ui::badge>

                            <i class="fa-solid fa-chevron-down text-xs
                                       text-slate-400 transition-transform duration-200
                                       group-open:rotate-180
                                       dark:text-neutral-500"
                                aria-hidden="true"></i>
                        </div>
                    </summary>

                    <div
                        class="border-t border-slate-200 px-5 py-5
                               dark:border-neutral-800 sm:px-6">
                        @if ($cancelada)
                            <div class="flex items-start gap-3">
                                <span
                                    class="flex h-9 w-9 shrink-0 items-center
                                           justify-center rounded-lg bg-red-50
                                           text-red-600
                                           dark:bg-red-950/40 dark:text-red-400">
                                    <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-sm font-semibold
                                               text-slate-900 dark:text-neutral-100">
                                        Votação cancelada
                                    </p>

                                    <p
                                        class="mt-1 text-sm
                                               text-slate-500 dark:text-neutral-400">
                                        Esta votação foi cancelada antes da apuração
                                        e não produziu resultado.
                                    </p>

                                    <p
                                        class="mt-3 rounded-lg bg-red-50 px-3 py-2
                                               text-sm text-red-700
                                               dark:bg-red-950/40 dark:text-red-300">
                                        <span class="font-semibold">Motivo:</span>
                                        {{ $votacaoFinalizada->motivo_cancelamento ?? 'Motivo não informado.' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <p
                                class="whitespace-pre-line text-sm leading-6
                                       text-slate-600 dark:text-neutral-300">
                                {{ $proposicao->ementa }}
                            </p>

                            <dl class="mt-4 grid grid-cols-3 gap-3">
                                <div
                                    class="rounded-lg bg-emerald-50 p-3 text-center
                                           dark:bg-emerald-950/50">
                                    <dt
                                        class="text-xs font-medium
                                               text-emerald-700 dark:text-emerald-300">
                                        Favoráveis
                                    </dt>

                                    <dd
                                        class="mt-1 text-lg font-semibold
                                               text-emerald-800 dark:text-emerald-200">
                                        {{ $favoraveis }}
                                    </dd>
                                </div>

                                <div
                                    class="rounded-lg bg-red-50 p-3 text-center
                                           dark:bg-red-950/50">
                                    <dt
                                        class="text-xs font-medium
                                               text-red-700 dark:text-red-300">
                                        Contrários
                                    </dt>

                                    <dd
                                        class="mt-1 text-lg font-semibold
                                               text-red-800 dark:text-red-200">
                                        {{ $contrarios }}
                                    </dd>
                                </div>

                                <div
                                    class="rounded-lg bg-slate-100 p-3 text-center
                                           dark:bg-neutral-800">
                                    <dt
                                        class="text-xs font-medium
                                               text-slate-600 dark:text-neutral-300">
                                        Abstenções
                                    </dt>

                                    <dd
                                        class="mt-1 text-lg font-semibold
                                               text-slate-800 dark:text-neutral-100">
                                        {{ $abstencoes }}
                                    </dd>
                                </div>
                            </dl>

                            @if ($rotuloMeuVoto)
                                <div
                                    class="mt-4 flex items-center justify-between gap-3
                                           rounded-lg border border-slate-200 bg-slate-50
                                           px-3 py-2.5
                                           dark:border-neutral-800
                                           dark:bg-neutral-950/40">
                                    <span
                                        class="inline-flex items-center gap-2 text-sm
                                               font-medium text-slate-600
                                               dark:text-neutral-300">
                                        <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                                        Seu voto
                                    </span>

                                    <x-ui::badge :variant="$configuracaoMeuVoto['variante']">
                                        <i class="fa-solid {{ $configuracaoMeuVoto['icone'] }}" aria-hidden="true"></i>

                                        {{ $rotuloMeuVoto }}
                                    </x-ui::badge>
                                </div>
                            @endif
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </section>
@endif
