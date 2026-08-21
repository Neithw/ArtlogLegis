@php
    $presencaConfirmada = $presenca?->situacao === 'presente';

    $podeConfirmarPresenca = in_array($sessao->situacao, ['convocada', 'aberta', 'suspensa'], true);

    $rotuloPresenca = $presenca
        ? \App\Models\SessaoPresenca::SITUACOES[$presenca->situacao] ?? 'Não registrada'
        : 'Não registrada';
@endphp

<section
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
           dark:border-neutral-800 dark:bg-neutral-900">
    <header class="border-b border-slate-200 px-5 py-4 dark:border-neutral-800">
        <h3 class="flex items-center gap-2 font-semibold text-slate-950 dark:text-neutral-100">
            <i class="fa-solid fa-user-check text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>

            Presença na sessão
        </h3>
    </header>

    <div class="p-5">
        @if ($presencaConfirmada)
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                           bg-emerald-50 text-emerald-600
                           dark:bg-emerald-950/60 dark:text-emerald-300">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </div>

                <div>
                    <p class="font-semibold text-emerald-700 dark:text-emerald-300">
                        Presença confirmada
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-neutral-400">
                        Você está habilitado a participar das votações desta sessão.
                    </p>

                    <p class="mt-2 text-xs text-slate-500 dark:text-neutral-500">
                        Registro atualizado em
                        {{ $presenca->updated_at->format('d/m/Y \à\s H:i') }}.
                    </p>
                </div>
            </div>
        @else
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                           bg-amber-50 text-amber-600
                           dark:bg-amber-950/60 dark:text-amber-300">
                    <i class="fa-solid fa-clock" aria-hidden="true"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-slate-950 dark:text-neutral-100">
                        Presença {{ mb_strtolower($rotuloPresenca) }}
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-neutral-400">
                        Confirme sua presença para participar das votações da sessão.
                    </p>

                    @if ($presenca?->observacao)
                        <p
                            class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm
                                   text-slate-600 dark:bg-neutral-950/60 dark:text-neutral-400">
                            <span class="font-semibold">Observação:</span>
                            {{ $presenca->observacao }}
                        </p>
                    @endif
                </div>
            </div>

            @if ($podeConfirmarPresenca)
                <form method="POST" action="{{ route('plenario.sessoes.presenca', $sessao) }}" x-data="{ enviando: false }"
                    @submit="enviando = true" class="mt-5">
                    @csrf

                    <button type="submit" :disabled="enviando"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl
                               bg-indigo-600 px-5 py-3 text-sm font-semibold text-white
                               transition hover:bg-indigo-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60
                               sm:w-auto dark:focus:ring-offset-neutral-900">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>

                        <span x-show="! enviando">
                            Confirmar minha presença
                        </span>

                        <span x-cloak x-show="enviando">
                            Confirmando...
                        </span>
                    </button>
                </form>
            @else
                <p
                    class="mt-5 rounded-lg bg-slate-50 px-4 py-3 text-sm
                           text-slate-600 dark:bg-neutral-950/60 dark:text-neutral-400">
                    O período para confirmação de presença está encerrado.
                </p>
            @endif
        @endif
    </div>
</section>
