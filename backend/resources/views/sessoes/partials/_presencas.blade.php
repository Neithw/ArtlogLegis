@if ($deveExibirPresencas)
    @php
        $totalMandatos = $mandatos->count();
        $totalPresentes = $presencasPorMandato->where('situacao', 'presente')->count();
        $totalAusentes = $presencasPorMandato->where('situacao', 'ausente')->count();
        $totalJustificadas = $presencasPorMandato->where('situacao', 'justificada')->count();
        $totalNaoRegistradas = max($totalMandatos - $presencasPorMandato->count(), 0);

        $registrosIniciais = $mandatos->mapWithKeys(function ($mandato) use ($presencasPorMandato) {
            $presenca = $presencasPorMandato->get($mandato->id);

            return [
                (string) $mandato->id => [
                    'situacao' => $presenca?->situacao ?? '',

                    'rotulo' => \App\Models\SessaoPresenca::SITUACOES[$presenca?->situacao] ?? 'Não registrada',

                    'observacao' => $presenca?->observacao ?? '',

                    'registradoPor' => $presenca?->registradoPor?->name,

                    'atualizadoPor' => $presenca?->atualizadoPor?->name,
                ],
            ];
        });
    @endphp

    <section x-data="presencasSessao(@js([
    'totais' => [
        'mandatos' => $totalMandatos,
        'presentes' => $totalPresentes,
        'ausentes' => $totalAusentes,
        'justificadas' => $totalJustificadas,
        'naoRegistradas' => $totalNaoRegistradas,
    ],
    'registros' => $registrosIniciais,
]))"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
               dark:border-neutral-800 dark:bg-neutral-900">

        <div x-cloak x-show="mensagem" x-transition.opacity
            class="fixed right-6 top-20 z-50 flex items-center gap-3 rounded-xl
           border border-emerald-800 bg-emerald-950 px-5 py-4
           text-sm font-medium text-emerald-300 shadow-xl"
            role="status" aria-live="polite">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>

            <span x-text="mensagem"></span>
        </div>

        <header
            class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5
                   sm:flex-row sm:items-center sm:justify-between
                   dark:border-neutral-800">
            <div>
                <h3
                    class="flex items-center gap-2 text-base font-semibold
                           text-slate-950 dark:text-neutral-100">
                    <i class="fa-solid fa-user-check text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>

                    Presenças da sessão
                </h3>

                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                    Registro dos parlamentares presentes e ausentes.
                </p>
            </div>

            <span
                class="inline-flex self-start items-center rounded-full bg-slate-100
           px-3 py-1.5 text-xs font-semibold text-slate-600
           dark:bg-neutral-800 dark:text-neutral-300">
                <span x-text="totais.mandatos">{{ $totalMandatos }}</span>

                <span class="ml-1" x-text="totais.mandatos === 1 ? 'mandato' : 'mandatos'">
                    {{ $totalMandatos === 1 ? 'mandato' : 'mandatos' }}
                </span>
            </span>
        </header>

        @if ($errors->has('presenca') || $errors->has('mandato') || $errors->has('situacao') || $errors->has('observacao'))
            <div class="border-b border-red-200 bg-red-50 px-6 py-4 dark:border-red-900 dark:bg-red-950/40">
                @foreach (['presenca', 'mandato', 'situacao', 'observacao'] as $campo)
                    @error($campo)
                        <p class="text-sm text-red-700 dark:text-red-300">
                            {{ $message }}
                        </p>
                    @enderror
                @endforeach
            </div>
        @endif

        <div
            class="grid gap-px border-b border-slate-200 bg-slate-200
                   sm:grid-cols-2 lg:grid-cols-4
                   dark:border-neutral-800 dark:bg-neutral-800">
            <div class="bg-slate-50 px-5 py-4 dark:bg-neutral-950/50">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Presentes
                </p>
                <p class="mt-1 text-lg font-semibold text-emerald-700 dark:text-emerald-400">
                    <span x-text="totais.presentes">{{ $totalPresentes }}</span>
                </p>
            </div>

            <div class="bg-slate-50 px-5 py-4 dark:bg-neutral-950/50">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Ausentes
                </p>
                <p class="mt-1 text-lg font-semibold text-red-700 dark:text-red-400">
                    <span x-text="totais.ausentes">{{ $totalAusentes }}</span>
                </p>
            </div>

            <div class="bg-slate-50 px-5 py-4 dark:bg-neutral-950/50">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Justificadas
                </p>
                <p class="mt-1 text-lg font-semibold text-amber-700 dark:text-amber-400">
                    <span x-text="totais.justificadas">{{ $totalJustificadas }}</span>
                </p>
            </div>

            <div class="bg-slate-50 px-5 py-4 dark:bg-neutral-950/50">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Não registradas
                </p>
                <p class="mt-1 text-lg font-semibold text-slate-700 dark:text-neutral-300">
                    <span x-text="totais.naoRegistradas">{{ $totalNaoRegistradas }}</span>
                </p>
            </div>
        </div>

        <div class="divide-y divide-slate-200 dark:divide-neutral-800">
            @forelse ($mandatos as $mandato)
                @php
                    $presenca = $presencasPorMandato->get($mandato->id);

                    $nomeVereador = $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome;

                    $variantePresenca = match ($presenca?->situacao) {
                        'presente' => 'success',
                        'ausente' => 'danger',
                        'justificada' => 'warning',
                        default => 'neutral',
                    };

                    $situacaoPresenca = \App\Models\SessaoPresenca::SITUACOES[$presenca?->situacao] ?? 'Não registrada';
                @endphp

                <article class="p-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $nomeVereador }}
                                </h4>

                                @if ($presenca)
                                    <p x-show="false" class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                        Registrada por {{ $presenca->registradoPor->name }}

                                        @if ($presenca->atualizadoPor)
                                            · Atualizada por {{ $presenca->atualizadoPor->name }}
                                        @endif
                                    </p>
                                @endif

                                <p x-cloak x-show="registro({{ $mandato->id }}).registradoPor"
                                    class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                    Registrada por

                                    <span x-text="registro({{ $mandato->id }}).registradoPor"></span>

                                    <span x-show="registro({{ $mandato->id }}).atualizadoPor">
                                        · Atualizada por

                                        <span x-text="registro({{ $mandato->id }}).atualizadoPor"></span>
                                    </span>
                                </p>
                            </div>

                            <div class="flex items-center">
                                {{-- Fallback sem JavaScript --}}
                                <span x-show="false">
                                    <x-ui::badge :variant="$variantePresenca">
                                        {{ $situacaoPresenca }}
                                    </x-ui::badge>
                                </span>

                                <x-ui::badge x-cloak x-show="registro({{ $mandato->id }}).situacao === 'presente'"
                                    variant="success">
                                    Presente
                                </x-ui::badge>

                                <x-ui::badge x-cloak x-show="registro({{ $mandato->id }}).situacao === 'ausente'"
                                    variant="danger">
                                    Ausente
                                </x-ui::badge>

                                <x-ui::badge x-cloak x-show="registro({{ $mandato->id }}).situacao === 'justificada'"
                                    variant="warning">
                                    Ausência justificada
                                </x-ui::badge>

                                <x-ui::badge x-cloak x-show="!registro({{ $mandato->id }}).situacao"
                                    variant="neutral">
                                    Não registrada
                                </x-ui::badge>
                            </div>
                        </div>

                        @if ($podeGerenciarPresencas)
                            <form method="POST" action="{{ route('sessoes.presencas.salvar', [$sessao, $mandato]) }}"
                                x-on:submit.prevent="salvar($event, {{ $mandato->id }})" novalidate
                                class="grid gap-3 lg:grid-cols-[minmax(0,14rem)_minmax(0,1fr)_auto]
                                       lg:items-end">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="situacao_{{ $mandato->id }}"
                                        class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                        Situação
                                    </label>

                                    <select id="situacao_{{ $mandato->id }}" name="situacao"
                                        x-model="registro({{ $mandato->id }}).situacaoFormulario" required
                                        class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                                               text-sm text-slate-950 shadow-sm
                                               focus:border-indigo-500 focus:ring-indigo-500
                                               dark:border-neutral-800 dark:bg-neutral-950
                                               dark:text-neutral-100">
                                        <option value="">Selecione</option>

                                        @foreach (\App\Models\SessaoPresenca::SITUACOES as $valor => $rotulo)
                                            <option value="{{ $valor }}" @selected($presenca?->situacao === $valor)>
                                                {{ $rotulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <label for="observacao_{{ $mandato->id }}"
                                            class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                            Observação
                                        </label>

                                        <span class="text-xs text-slate-400 dark:text-neutral-500">
                                            <span x-text="registro({{ $mandato->id }}).observacaoFormulario.length">
                                                {{ mb_strlen($presenca?->observacao ?? '') }}
                                            </span>/1000
                                        </span>
                                    </div>

                                    <input id="observacao_{{ $mandato->id }}" name="observacao"
                                        x-model="registro({{ $mandato->id }}).observacaoFormulario"
                                        x-bind:required="registro({{ $mandato->id }}).situacaoFormulario
                                        === 'justificada'"
                                        type="text" maxlength="1000" value="{{ $presenca?->observacao }}"
                                        placeholder="Obrigatória para ausência justificada."
                                        class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                                               text-sm text-slate-950 shadow-sm
                                               placeholder:text-slate-400
                                               focus:border-indigo-500 focus:ring-indigo-500
                                               dark:border-neutral-800 dark:bg-neutral-950
                                               dark:text-neutral-100 dark:placeholder:text-neutral-600">
                                </div>

                                <x-ui::button type="submit" x-bind:disabled="registro({{ $mandato->id }}).salvando"
                                    class="disabled:cursor-wait disabled:opacity-70">
                                    <i x-show="!registro({{ $mandato->id }}).salvando" class="fa-solid fa-floppy-disk"
                                        aria-hidden="true"></i>

                                    <i x-cloak x-show="registro({{ $mandato->id }}).salvando"
                                        class="fa-solid fa-spinner animate-spin" aria-hidden="true"></i>

                                    <span x-text="registro({{ $mandato->id }}).salvando ? 'Salvando...' : 'Salvar'">
                                        Salvar
                                    </span>
                                </x-ui::button>
                            </form>

                            <p x-cloak x-show="registro({{ $mandato->id }}).erro"
                                x-text="registro({{ $mandato->id }}).erro"
                                class="text-sm text-red-600 dark:text-red-400" role="alert"></p>
                        @elseif ($presenca?->observacao)
                            <div class="rounded-lg bg-slate-50 px-4 py-3 dark:bg-neutral-950">
                                <p class="text-sm text-slate-600 dark:text-neutral-300">
                                    {{ $presenca->observacao }}
                                </p>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-6 py-10 text-center">
                    <i class="fa-solid fa-users-slash text-2xl text-slate-300 dark:text-neutral-700"
                        aria-hidden="true"></i>

                    <p class="mt-3 text-sm font-medium text-slate-700 dark:text-neutral-300">
                        Nenhum mandato vigente encontrado
                    </p>

                    <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                        Não existem parlamentares aptos para o registro nesta sessão.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
@endif
