@php
    $votacaoAberta = $itemPauta->votacoes->firstWhere('situacao', 'aberta');

    $votosPorMandato = $votacaoAberta ? $votacaoAberta->votos->keyBy('mandato_id') : collect();

    $ultimaVotacaoFinalizada = $itemPauta->votacoes->first(
        fn($votacao) => in_array($votacao->situacao, ['encerrada', 'cancelada'], true),
    );

    $deveExibirPainelVotacao =
        $votacaoAberta !== null ||
        $ultimaVotacaoFinalizada !== null ||
        ($sessao->situacao === 'aberta' && $itemPauta->situacao === 'pendente');
@endphp

@if ($deveExibirPainelVotacao)
    <div
        class="mt-5 rounded-xl border border-slate-200 bg-slate-50/70
           p-4 dark:border-neutral-800 dark:bg-neutral-950/40">
        @if ($votacaoAberta)
            <div class="flex flex-col gap-3
                   sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full
                               bg-emerald-500"
                            aria-hidden="true"></span>

                        <p
                            class="text-sm font-semibold
                               text-slate-900 dark:text-neutral-100">
                            Votação nominal aberta
                        </p>
                    </div>

                    <p class="mt-1 text-xs
                           text-slate-500 dark:text-neutral-400">
                        Aberta por {{ $votacaoAberta->abertaPor->name }}
                        em
                        {{ $votacaoAberta->aberta_em->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>

                <x-ui::badge variant="success">
                    <i class="fa-solid fa-circle-dot" aria-hidden="true"></i>

                    Em votação
                </x-ui::badge>
            </div>

            @if ($votacaoAberta->observacao)
                <p
                    class="mt-3 border-t border-slate-200 pt-3
                       text-sm text-slate-600
                       dark:border-neutral-800 dark:text-neutral-300">
                    {{ $votacaoAberta->observacao }}
                </p>
            @endif

            @if ($sessao->situacao === 'aberta' && $itemPauta->situacao === 'em_votacao')
                @can('registrarVoto', $votacaoAberta)
                    <div class="mt-4 border-t border-slate-200 pt-4
                   dark:border-neutral-800">
                        <div
                            class="mb-3 flex flex-col gap-1
                       sm:flex-row sm:items-center
                       sm:justify-between">
                            <div>
                                <h5
                                    class="text-sm font-semibold
                               text-slate-900 dark:text-neutral-100">
                                    Registro dos votos
                                </h5>

                                <p class="text-xs
                               text-slate-500 dark:text-neutral-400">
                                    Somente parlamentares com presença confirmada.
                                </p>
                            </div>

                            <span
                                class="text-xs font-semibold
                           text-slate-500 dark:text-neutral-400">
                                {{ $votosPorMandato->count() }}
                                de {{ $mandatosPresentes->count() }}
                                registrados
                            </span>
                        </div>

                        <div class="space-y-3">
                            @forelse ($mandatosPresentes as $mandato)
                                @php
                                    $voto = $votosPorMandato->get($mandato->id);

                                    $nomeParlamentar = $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome;

                                    $escolhaSelecionada =
                                        (int) old('mandato_id') === $mandato->id
                                            ? old('escolha', $voto?->escolha)
                                            : $voto?->escolha;
                                @endphp

                                <form method="POST" action="{{ route('votacoes.votos.registrar', $votacaoAberta) }}"
                                    class="grid gap-3 rounded-lg border
                               border-slate-200 bg-white p-3
                               sm:grid-cols-[minmax(0,1fr)_12rem_auto]
                               sm:items-center
                               dark:border-neutral-800
                               dark:bg-neutral-900">
                                    @csrf

                                    <input type="hidden" name="mandato_id" value="{{ $mandato->id }}">

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-medium
                                       text-slate-800
                                       dark:text-neutral-200">
                                            {{ $nomeParlamentar }}
                                        </p>

                                        <p
                                            class="mt-0.5 text-xs
                                       text-slate-500
                                       dark:text-neutral-400">
                                            {{ $voto ? 'Voto registrado' : 'Aguardando voto' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="escolha_{{ $votacaoAberta->id }}_{{ $mandato->id }}" class="sr-only">
                                            Voto de {{ $nomeParlamentar }}
                                        </label>

                                        <select id="escolha_{{ $votacaoAberta->id }}_{{ $mandato->id }}" name="escolha"
                                            required
                                            class="block w-full rounded-lg
                                       border-slate-300 bg-white
                                       text-sm text-slate-950
                                       shadow-sm
                                       focus:border-indigo-500
                                       focus:ring-indigo-500
                                       dark:border-neutral-700
                                       dark:bg-neutral-950
                                       dark:text-neutral-100">
                                            <option value="">
                                                Selecione
                                            </option>

                                            @foreach (\App\Models\Voto::ESCOLHAS as $codigo => $rotulo)
                                                <option value="{{ $codigo }}" @selected($escolhaSelecionada === $codigo)>
                                                    {{ $rotulo }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @if ((int) old('mandato_id') === $mandato->id)
                                            <x-input-error for="mandato_id" class="mt-2 dark:text-red-400" />

                                            <x-input-error for="escolha" class="mt-2 dark:text-red-400" />
                                        @endif
                                    </div>

                                    <x-ui::button type="submit">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>

                                        {{ $voto ? 'Atualizar' : 'Registrar' }}
                                    </x-ui::button>
                                </form>
                            @empty
                                <div
                                    class="rounded-lg border border-dashed
                               border-slate-300 px-4 py-5
                               text-center text-sm text-slate-500
                               dark:border-neutral-700
                               dark:text-neutral-400">
                                    Nenhum parlamentar possui presença confirmada.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endcan

                @can('encerrar', $votacaoAberta)
                    <div
                        class="mt-4 flex flex-col gap-4 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-neutral-100">
                                Encerramento da votação
                            </p>

                            <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                O resultado será calculado com base nos votos registrados.
                            </p>

                            @if ($votosPorMandato->isEmpty())
                                <p class="mt-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                    Registre ao menos um voto antes de encerrar.
                                </p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('votacoes.encerrar', $votacaoAberta) }}"
                            onsubmit="return confirm('Deseja encerrar esta votação e apurar o resultado?');">
                            @csrf
                            @method('PATCH')

                            <button type="submit" @disabled($votosPorMandato->isEmpty())
                                class="inline-flex w-full items-center justify-center gap-2
                                    rounded-lg bg-indigo-600 px-4 py-2.5
                                    text-sm font-semibold text-white transition
                                    hover:bg-indigo-700
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500
                                    disabled:cursor-not-allowed disabled:opacity-50
                                    sm:w-auto">
                                <i class="fa-solid fa-square-poll-vertical" aria-hidden="true"></i>

                                Encerrar e apurar
                            </button>
                        </form>
                    </div>
                @endcan
                @can('cancelar', $votacaoAberta)
                    <details @if ($errors->has('motivo_cancelamento')) open @endif
                        class="group mt-4 border-t border-slate-200 pt-4 dark:border-neutral-800">
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between gap-3
                                rounded-lg text-sm font-semibold text-red-600
                                focus:outline-none focus:ring-2 focus:ring-red-500
                                dark:text-red-400">
                            <span class="inline-flex items-center gap-2">
                                <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                Cancelar votação
                            </span>

                            <i class="fa-solid fa-chevron-down text-xs transition-transform group-open:rotate-180"
                                aria-hidden="true"></i>
                        </summary>

                        <form method="POST" action="{{ route('votacoes.cancelar', $votacaoAberta) }}"
                            onsubmit="return confirm('Deseja realmente cancelar esta votação?');" class="mt-4">
                            @csrf
                            @method('PATCH')

                            <label for="motivo_cancelamento_{{ $votacaoAberta->id }}"
                                class="block text-sm font-medium
                       text-slate-700 dark:text-neutral-300">
                                Motivo do cancelamento
                            </label>

                            <textarea id="motivo_cancelamento_{{ $votacaoAberta->id }}" name="motivo_cancelamento" rows="3" maxlength="1000"
                                required
                                class="mt-1 block w-full rounded-lg
                                    border-slate-300 bg-white text-sm
                                    text-slate-950 shadow-sm
                                    focus:border-red-500 focus:ring-red-500
                                    dark:border-neutral-800
                                    dark:bg-neutral-950
                                    dark:text-neutral-100">{{ old('motivo_cancelamento') }}</textarea>

                            <x-input-error for="motivo_cancelamento" class="mt-2 dark:text-red-400" />

                            <div class="mt-3 flex justify-end">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2
                                        rounded-lg bg-red-600 px-4 py-2.5
                                        text-sm font-semibold text-white transition
                                        hover:bg-red-700
                                        focus:outline-none focus:ring-2 focus:ring-red-500
                                        sm:w-auto">
                                    <i class="fa-solid fa-ban" aria-hidden="true"></i>

                                    Confirmar cancelamento
                                </button>
                            </div>
                        </form>
                    </details>
                @endcan
            @endif
        @elseif ($sessao->situacao === 'aberta' && $itemPauta->situacao === 'pendente' && $votacaoAbertaDaSessao === null)
            @can('abrir', [\App\Models\Votacao::class, $itemPauta])
                <form method="POST" action="{{ route('votacoes.abrir', $itemPauta) }}" class="space-y-3">
                    @csrf

                    <div>
                        <label for="observacao_votacao_{{ $itemPauta->id }}"
                            class="block text-sm font-medium
                               text-slate-700 dark:text-neutral-300">
                            Observação da votação
                            <span class="font-normal text-slate-400">
                                (opcional)
                            </span>
                        </label>

                        <textarea id="observacao_votacao_{{ $itemPauta->id }}" name="observacao" rows="2" maxlength="1000"
                            class="mt-1 block w-full rounded-lg
                               border-slate-300 bg-white text-sm
                               text-slate-950 shadow-sm
                               focus:border-indigo-500
                               focus:ring-indigo-500
                               dark:border-neutral-800
                               dark:bg-neutral-950
                               dark:text-neutral-100">{{ old('observacao') }}</textarea>

                        <x-input-error for="observacao" class="mt-2 dark:text-red-400" />
                    </div>

                    <div class="flex justify-end">
                        <x-ui::button type="submit">
                            <i class="fa-solid fa-check-to-slot" aria-hidden="true"></i>

                            Abrir votação
                        </x-ui::button>
                    </div>
                </form>
            @else
                <p class="text-sm text-slate-500 dark:text-neutral-400">
                    Nenhuma votação aberta para este item.
                </p>
            @endcan
        @elseif ($sessao->situacao === 'aberta' && $itemPauta->situacao === 'pendente' && $votacaoAbertaDaSessao !== null)
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-info mt-0.5 text-amber-500" aria-hidden="true"></i>

                <p class="text-sm text-slate-600 dark:text-neutral-400">
                    Outro item da pauta está em votação. Encerre ou cancele
                    a votação atual antes de iniciar uma nova.
                </p>
            </div>
        @endif

        @if ($ultimaVotacaoFinalizada)
            @php
                $votosFinalizados = $ultimaVotacaoFinalizada->votos;

                $totais = [
                    'favoraveis' => $votosFinalizados->where('escolha', 'favoravel')->count(),

                    'contrarios' => $votosFinalizados->where('escolha', 'contrario')->count(),

                    'abstencoes' => $votosFinalizados->where('escolha', 'abstencao')->count(),
                ];

                $votacaoCancelada = $ultimaVotacaoFinalizada->situacao === 'cancelada';

                $rotuloResultado = $votacaoCancelada
                    ? 'Cancelada'
                    : \App\Models\Votacao::RESULTADOS[$ultimaVotacaoFinalizada->resultado] ?? 'Sem resultado';

                $varianteResultado = $votacaoCancelada
                    ? 'danger'
                    : match ($ultimaVotacaoFinalizada->resultado) {
                        'aprovada' => 'success',
                        'rejeitada' => 'danger',
                        'empate' => 'warning',
                        'sem_decisao' => 'neutral',
                        default => 'neutral',
                    };

                $responsavelFinalizacao = $votacaoCancelada
                    ? $ultimaVotacaoFinalizada->canceladaPor
                    : $ultimaVotacaoFinalizada->encerradaPor;

                $dataFinalizacao = $votacaoCancelada
                    ? $ultimaVotacaoFinalizada->cancelada_em
                    : $ultimaVotacaoFinalizada->encerrada_em;
            @endphp

            <div class="mt-2 border-t border-slate-200 pt-2
                dark:border-neutral-800">
                <div class="flex flex-col gap-3
                    sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p
                            class="text-sm font-semibold
                          text-slate-900 dark:text-neutral-100">
                            Última votação finalizada
                        </p>

                        <p class="mt-1 text-xs
                          text-slate-500 dark:text-neutral-400">
                            Finalizada por
                            {{ $responsavelFinalizacao?->name ?? 'usuário não identificado' }}
                            em
                            {{ $dataFinalizacao?->format('d/m/Y \à\s H:i') ?? 'data não informada' }}.
                        </p>
                    </div>

                    <x-ui::badge :variant="$varianteResultado">
                        {{ $rotuloResultado }}
                    </x-ui::badge>
                </div>

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
                            {{ $totais['favoraveis'] }}
                        </dd>
                    </div>

                    <div class="rounded-lg bg-red-50 p-3 text-center
                        dark:bg-red-950/50">
                        <dt class="text-xs font-medium
                           text-red-700 dark:text-red-300">
                            Contrários
                        </dt>

                        <dd
                            class="mt-1 text-lg font-semibold
                           text-red-800 dark:text-red-200">
                            {{ $totais['contrarios'] }}
                        </dd>
                    </div>

                    <div class="rounded-lg bg-slate-100 p-3 text-center
                        dark:bg-neutral-800">
                        <dt
                            class="text-xs font-medium
                           text-slate-600 dark:text-neutral-300">
                            Abstenções
                        </dt>

                        <dd
                            class="mt-1 text-lg font-semibold
                           text-slate-800 dark:text-neutral-100">
                            {{ $totais['abstencoes'] }}
                        </dd>
                    </div>
                </dl>

                @if ($votacaoCancelada)
                    <p
                        class="mt-4 rounded-lg bg-red-50 px-3 py-2
                      text-sm text-red-700
                      dark:bg-red-950/40 dark:text-red-300">
                        <span class="font-semibold">
                            Motivo do cancelamento:
                        </span>

                        {{ $ultimaVotacaoFinalizada->motivo_cancelamento }}
                    </p>
                @endif
            </div>
        @endif
    </div>
@endif
