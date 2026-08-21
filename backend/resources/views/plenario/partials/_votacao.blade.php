@php
    $presencaConfirmada = $presenca?->situacao === 'presente';
    $proposicao = $votacaoAberta?->itemPauta?->proposicao;

    $rotuloVotoAtual = $meuVoto ? \App\Models\Voto::ESCOLHAS[$meuVoto->escolha] ?? $meuVoto->escolha : null;
@endphp

<section
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
           dark:border-neutral-800 dark:bg-neutral-900">
    <header
        class="flex items-center justify-between gap-3 border-b border-slate-200
               px-5 py-4 dark:border-neutral-800">
        <h3 class="flex items-center gap-2 font-semibold text-slate-950 dark:text-neutral-100">
            <i class="fa-solid fa-check-to-slot text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>

            Votação
        </h3>

        @if ($votacaoAberta && $sessao->situacao === 'aberta')
            <span
                class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5
                       text-xs font-semibold text-emerald-700
                       dark:bg-emerald-950 dark:text-emerald-300">
                Em andamento
            </span>
        @elseif ($votacaoAberta)
            <span
                class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5
                       text-xs font-semibold text-amber-700
                       dark:bg-amber-950 dark:text-amber-300">
                <i class="fa-solid fa-pause" aria-hidden="true"></i>
                Pausada
            </span>
        @elseif ($sessao->situacao === 'encerrada')
            <span
                class="inline-flex items-center gap-2 rounded-full
               bg-slate-100 px-3 py-1.5 text-xs font-semibold
               text-slate-600
               dark:bg-neutral-800 dark:text-neutral-300">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>

                Encerrada
            </span>
        @else
            <span
                class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5
                       text-xs font-semibold text-slate-600
                       dark:bg-neutral-800 dark:text-neutral-300">
                <i class="fa-solid fa-clock" aria-hidden="true"></i>
                Aguardando
            </span>
        @endif
    </header>

    @if ($votacaoAberta === null)
        <div class="p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                           bg-slate-100 text-slate-500
                           dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid {{ $sessao->situacao === 'encerrada' ? 'fa-circle-check' : 'fa-hourglass-half' }}"
                        aria-hidden="true"></i>
                </div>

                <div>
                    <p class="font-semibold text-slate-950 dark:text-neutral-100">
                        {{ $sessao->situacao === 'encerrada' ? 'Sessão encerrada' : 'Nenhuma votação aberta' }}
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-neutral-400">
                        {{ $sessao->situacao === 'encerrada'
                            ? 'Consulte abaixo os resultados das votações realizadas.'
                            : 'Quando a mesa iniciar uma votação, o item aparecerá aqui.' }}
                    </p>
                </div>
            </div>
        </div>
    @elseif ($sessao->situacao !== 'aberta')
        <div class="p-5 sm:p-6">
            <div
                class="rounded-xl bg-amber-50 p-4 text-amber-800
                       dark:bg-amber-950 dark:text-amber-200">
                <p class="font-semibold">
                    Votação temporariamente indisponível
                </p>

                <p class="mt-1 text-sm leading-6">
                    A sessão precisa estar aberta para que votos sejam registrados.
                </p>
            </div>
        </div>
    @elseif (!$presencaConfirmada)
        <div class="p-5 sm:p-6">
            <div
                class="rounded-xl bg-amber-50 p-4 text-amber-800
                       dark:bg-amber-950 dark:text-amber-200">
                <p class="font-semibold">
                    Confirme sua presença
                </p>

                <p class="mt-1 text-sm leading-6">
                    A votação está aberta, mas sua presença ainda não foi confirmada.
                </p>
            </div>
        </div>
    @else
        <div x-data="votacaoPlenario({
            votoAtual: @js($meuVoto?->escolha),
            escolhaInicial: @js(old('escolha')),
            rotulos: @js(\App\Models\Voto::ESCOLHAS)
        })" class="p-5 sm:p-6">
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    Item {{ $votacaoAberta->itemPauta->ordem }} da pauta
                </p>

                <h4 class="mt-1 text-lg font-semibold text-slate-950 dark:text-neutral-100">
                    {{ $proposicao->tipoProposicao->nome }}
                    nº {{ $proposicao->numero }}/{{ $proposicao->ano }}
                </h4>

                {{-- blade-formatter-disable-next-line --}}
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-neutral-300">{{ $proposicao->ementa }}</p>

                @if ($votacaoAberta->observacao)
                    <div
                        class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-sm
                               text-slate-600 dark:bg-neutral-800 dark:text-neutral-300">
                        <span class="font-semibold">Observação da votação:</span>
                        {{ $votacaoAberta->observacao }}
                    </div>
                @endif

                @if ($proposicao->texto_integral)
                    <details
                        class="mt-4 rounded-xl border border-slate-200
                               dark:border-neutral-800">
                        <summary
                            class="cursor-pointer px-4 py-3 text-sm font-semibold
                                   text-indigo-600 dark:text-indigo-400">
                            Consultar texto integral
                        </summary>

                        <div
                            class="border-t border-slate-200 px-4 py-4
                                   dark:border-neutral-800">
                            <p class="whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-neutral-300">
                                {{ $proposicao->texto_integral }}</p>
                        </div>
                    </details>
                @endif
            </div>

            @if ($meuVoto)
                <div
                    class="mt-5 rounded-xl bg-indigo-50 p-4 text-indigo-800
               dark:bg-neutral-800 dark:text-indigo-200">
                    <p class="text-sm font-semibold">
                        Seu voto atual: {{ $rotuloVotoAtual }}
                    </p>

                    <p class="mt-1 text-xs">
                        Você pode alterá-lo enquanto a votação permanecer aberta.
                    </p>
                </div>
            @endif

            <fieldset class="mt-6">
                <legend class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                    Selecione seu voto
                </legend>

                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <button type="button" @click="selecionar('favoravel')" :aria-pressed="estaSelecionado('favoravel')"
                        :class="{
                            'ring-2 ring-emerald-500': estaSelecionado('favoravel')
                        }"
                        @class([
                            'flex min-h-24 flex-col items-center justify-center gap-2 rounded-xl',
                            'border border-emerald-200 bg-emerald-50 px-4 py-4',
                            'font-semibold text-emerald-700 transition',
                            'hover:border-emerald-400 hover:bg-emerald-100',
                            'focus:outline-none focus:ring-2 focus:ring-emerald-500',
                            'dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300',
                        ])>
                        <i class="fa-solid fa-thumbs-up text-xl" aria-hidden="true"></i>
                        Favorável
                        <span x-cloak x-show="ehVotoAtual('favoravel')" class="text-xs font-semibold opacity-80">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Voto atual
                        </span>

                        <span x-cloak x-show="ehNovaEscolha('favoravel')" class="text-xs font-semibold">
                            Nova escolha
                        </span>
                    </button>

                    <button type="button" @click="selecionar('contrario')" :aria-pressed="estaSelecionado('contrario')"
                        :class="{
                            'ring-2 ring-red-500': estaSelecionado('contrario')
                        }"
                        @class([
                            'flex min-h-24 flex-col items-center justify-center gap-2 rounded-xl',
                            'border border-red-200 bg-red-50 px-4 py-4',
                            'font-semibold text-red-700 transition',
                            'hover:border-red-400 hover:bg-red-100',
                            'focus:outline-none focus:ring-2 focus:ring-red-500',
                            'dark:border-red-900 dark:bg-red-950 dark:text-red-300',
                        ])>
                        <i class="fa-solid fa-thumbs-down text-xl" aria-hidden="true"></i>
                        Contrário
                        <span x-cloak x-show="ehVotoAtual('contrario')" class="text-xs font-semibold opacity-80">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Voto atual
                        </span>

                        <span x-cloak x-show="ehNovaEscolha('contrario')" class="text-xs font-semibold">
                            Nova escolha
                        </span>
                    </button>

                    <button type="button" @click="selecionar('abstencao')" :aria-pressed="estaSelecionado('abstencao')"
                        :class="{
                            'ring-2 ring-slate-500': estaSelecionado('abstencao')
                        }"
                        @class([
                            'flex min-h-24 flex-col items-center justify-center gap-2 rounded-xl',
                            'border border-slate-200 bg-slate-50 px-4 py-4',
                            'font-semibold text-slate-700 transition',
                            'hover:border-slate-400 hover:bg-slate-100',
                            'focus:outline-none focus:ring-2 focus:ring-slate-500',
                            'dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300',
                        ])>
                        <i class="fa-solid fa-minus text-xl" aria-hidden="true"></i>
                        Abstenção
                        <span x-cloak x-show="ehVotoAtual('abstencao')" class="text-xs font-semibold opacity-80">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            Voto atual
                        </span>

                        <span x-cloak x-show="ehNovaEscolha('abstencao')" class="text-xs font-semibold">
                            Nova escolha
                        </span>
                    </button>
                </div>
            </fieldset>

            <div x-cloak x-show="escolhaPendente !== null" x-collapse.duration.400ms
                @transitionend.self="aoFinalizarExpansao($event)" class="overflow-hidden">
                <div class="pt-5">
                    <div x-ref="confirmacao"
                        class="rounded-xl border border-indigo-200
                                bg-indigo-50 p-4
                                dark:border-indigo-900 dark:bg-neutral-800">

                        <h4 x-ref="tituloConfirmacao" tabindex="-1"
                            class="font-semibold text-slate-950 focus:outline-none dark:text-neutral-100"
                            x-text="votoAtual !== null
                            ? 'Confirmar alteração de voto'
                            : 'Confirmar voto'">
                        </h4>

                        <p x-show="votoAtual !== null" class="mt-1 text-sm text-slate-600 dark:text-neutral-300">
                            Você está alterando seu voto de
                            <strong x-text="rotulo(votoAtual)"></strong>
                            para
                            <strong x-text="rotulo(escolhaPendente)"></strong>.
                        </p>

                        <p x-show="votoAtual === null" class="mt-1 text-sm text-slate-600 dark:text-neutral-300">
                            Você selecionou
                            <strong x-text="rotulo(escolhaPendente)"></strong>.
                            Confirme para registrar o voto.
                        </p>

                        <form method="POST" action="{{ route('plenario.votos.registrar', $votacaoAberta) }}"
                            @submit="confirmar($event)"
                            class="mt-4 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            @csrf

                            <input type="hidden" name="escolha" :value="escolhaPendente">

                            <button type="button" @click="cancelar()" :disabled="enviando"
                                class="rounded-lg px-4 py-2.5 text-sm font-semibold
                                text-slate-600 transition hover:bg-white
                                disabled:cursor-wait disabled:opacity-60
                                dark:text-neutral-300 dark:hover:bg-neutral-700">
                                Voltar
                            </button>

                            <button type="submit" :disabled="!podeConfirmar"
                                class="inline-flex items-center justify-center gap-2 rounded-lg
                               bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white
                               transition hover:bg-indigo-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-500
                               disabled:cursor-wait disabled:opacity-60">
                                <i class="fa-solid fa-check" aria-hidden="true"></i>

                                <span x-show="! enviando"
                                    x-text="votoAtual !== null
                                    ? 'Confirmar alteração'
                                    : 'Confirmar voto'">
                                </span>

                                <span x-cloak x-show="enviando">
                                    Registrando...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
