<section x-data="pautaOrdenavel" :aria-busy="processando"
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">

    <header
        class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5
               sm:flex-row sm:items-center sm:justify-between
               dark:border-neutral-800">
        <div>
            <h3
                class="flex items-center gap-2 text-base font-semibold
                       text-slate-950 dark:text-neutral-100">
                <i class="fa-solid fa-list-ol text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>

                Pauta da sessão
            </h3>

            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                Proposições organizadas para apreciação nesta sessão.
            </p>
        </div>

        <span
            class="inline-flex self-start items-center rounded-full bg-slate-100
                   px-3 py-1.5 text-xs font-semibold text-slate-600
                   dark:bg-neutral-800 dark:text-neutral-300">
            {{ $sessao->itensPauta->count() }}
            {{ $sessao->itensPauta->count() === 1 ? 'item' : 'itens' }}
        </span>
    </header>

    @if ($podeGerenciarPauta)
        <div
            class="border-b border-slate-200 bg-slate-50/70 px-6 py-5
               dark:border-neutral-800 dark:bg-neutral-950/40">
            @if ($proposicoesDisponiveis->isNotEmpty())
                <form method="POST" action="{{ route('sessoes.pauta.store', $sessao) }}"
                    class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    @csrf

                    <div class="min-w-0 flex-1">
                        <label for="proposicao_id"
                            class="block text-sm font-medium text-slate-700
                               dark:text-neutral-300">
                            Incluir proposição
                        </label>

                        <select id="proposicao_id" name="proposicao_id" required
                            class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                               text-sm text-slate-950 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500
                               dark:border-neutral-800 dark:bg-neutral-950
                               dark:text-neutral-100">
                            <option value="">Selecione uma proposição</option>

                            @foreach ($proposicoesDisponiveis as $proposicao)
                                <option value="{{ $proposicao->id }}" @selected(old('proposicao_id') == $proposicao->id)>
                                    {{ $proposicao->tipoProposicao->nome }}
                                    nº {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                    — {{ \Illuminate\Support\Str::limit($proposicao->ementa, 80) }}
                                </option>
                            @endforeach
                        </select>

                        <x-input-error for="proposicao_id" class="mt-2 dark:text-red-400" />
                    </div>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Incluir na pauta
                    </x-ui::button>
                </form>
            @else
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                           bg-slate-100 text-slate-400
                           dark:bg-neutral-800 dark:text-neutral-500">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                            Todas as proposições elegíveis já estão na pauta
                        </p>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Não existem outras proposições protocoladas disponíveis para esta sessão.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div x-cloak x-show="erro" x-transition.opacity
        class="mx-6 mt-5 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700
           dark:bg-red-950/40 dark:text-red-300"
        role="alert" x-text="erro">
    </div>

    <p class="sr-only" aria-live="polite" x-text="mensagem">
    </p>

    <div x-ref="lista" class="p-6">
        @forelse ($sessao->itensPauta as $itemPauta)
            <article data-item-pauta data-item-id="{{ $itemPauta->id }}"
                class="flex gap-4 border-b border-slate-200 py-5 first:pt-0 last:border-0 last:pb-0
                       dark:border-neutral-800">
                <div data-numero-ordem
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                           bg-indigo-50 text-sm font-semibold text-indigo-700
                           dark:bg-indigo-950/60 dark:text-indigo-300">
                    {{ $itemPauta->ordem }}
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h4 class="font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $itemPauta->proposicao->tipoProposicao->nome }}
                                nº
                                {{ $itemPauta->proposicao->numero }}/{{ $itemPauta->proposicao->ano }}
                            </h4>

                            <a href="{{ route('proposicoes.show', $itemPauta->proposicao) }}"
                                class="inline-flex items-center gap-1.5 font-semibold text-indigo-600
                                   transition hover:text-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   dark:text-indigo-400 dark:hover:text-indigo-300">
                                Ver proposição

                                <i class="fa-solid fa-arrow-up-right-from-square text-[0.65rem]" aria-hidden="true"></i>
                            </a>

                            {{-- blade-formatter-disable-next-line --}}
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-neutral-300">{{ $itemPauta->proposicao->ementa }}</p>
                        </div>

                        <div class="flex shrink-0 flex-wrap items-center justify-end gap-2">
                            {{-- Ordenação assíncrona --}}
                            @if ($podeGerenciarPauta && $sessao->itensPauta->count() > 1)
                                <div
                                    class="flex items-center rounded-lg border border-slate-200 p-0.5
                   dark:border-neutral-700">
                                    <form method="POST"
                                        action="{{ route('sessoes.pauta.mover', [$sessao, $itemPauta]) }}"
                                        data-mover="acima" @submit.prevent="mover($event)" @class(['hidden' => $loop->first])>
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden" name="direcao" value="acima">

                                        <button type="submit" :disabled="processando"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md
                           text-slate-500 transition hover:bg-slate-100
                           hover:text-slate-700 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500
                           disabled:cursor-wait disabled:opacity-50
                           dark:text-neutral-400 dark:hover:bg-neutral-800
                           dark:hover:text-neutral-200"
                                            title="Mover para cima"
                                            aria-label="Mover item {{ $itemPauta->ordem }} para cima">
                                            <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('sessoes.pauta.mover', [$sessao, $itemPauta]) }}"
                                        data-mover="abaixo" @submit.prevent="mover($event)"
                                        @class(['hidden' => $loop->last])>
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden" name="direcao" value="abaixo">

                                        <button type="submit" :disabled="processando"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-md
                           text-slate-500 transition hover:bg-slate-100
                           hover:text-slate-700 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500
                           disabled:cursor-wait disabled:opacity-50
                           dark:text-neutral-400 dark:hover:bg-neutral-800
                           dark:hover:text-neutral-200"
                                            title="Mover para baixo"
                                            aria-label="Mover item {{ $itemPauta->ordem }} para baixo">
                                            <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- Situação --}}
                            <x-ui::badge variant="warning">
                                <i class="fa-solid fa-clock w-3 shrink-0 text-center leading-none"
                                    aria-hidden="true"></i>

                                {{ ucfirst(str_replace('_', ' ', $itemPauta->situacao)) }}
                            </x-ui::badge>

                            {{-- Remoção --}}
                            @if ($podeGerenciarPauta)
                                <form method="POST"
                                    action="{{ route('sessoes.pauta.destroy', [$sessao, $itemPauta]) }}"
                                    onsubmit="return confirm('Deseja remover esta proposição da pauta?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5
                       text-xs font-semibold text-red-600 transition
                       hover:bg-red-50 focus:outline-none focus:ring-2
                       focus:ring-red-500
                       dark:text-red-400 dark:hover:bg-red-950/40"
                                        aria-label="Remover proposição da pauta">
                                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        Remover
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div
                        class="mt-4 flex flex-col gap-2 text-xs text-slate-500
                               sm:flex-row sm:items-center sm:justify-between
                               dark:text-neutral-400">
                        <span>
                            Incluída por {{ $itemPauta->incluidoPor->name }}
                            em {{ $itemPauta->created_at->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>
                </div>
            </article>
        @empty
            <div class="py-8 text-center">
                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full
                           bg-slate-100 text-slate-400
                           dark:bg-neutral-800 dark:text-neutral-500">
                    <i class="fa-solid fa-list-ol" aria-hidden="true"></i>
                </div>

                <p class="mt-4 text-sm font-medium text-slate-700 dark:text-neutral-300">
                    Nenhuma proposição na pauta
                </p>

                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                    As proposições incluídas para apreciação aparecerão aqui.
                </p>
            </div>
        @endforelse
    </div>
</section>
