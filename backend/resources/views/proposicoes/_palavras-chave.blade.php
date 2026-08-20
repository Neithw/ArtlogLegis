@php
    $palavrasChaveIniciais = collect(old('palavras_chave', isset($proposicao) ? $proposicao->palavras_chave ?? [] : []))
        ->filter(fn($valor) => is_string($valor) && trim($valor) !== '')
        ->map(fn($valor) => trim($valor))
        ->values();
@endphp

<div class="md:col-span-2" x-data="campoPalavrasChave(
    @js($palavrasChaveIniciais)
)">
    <label for="nova_palavra_chave" class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
        Palavras-chave
    </label>

    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
        Digite um termo e pressione Enter para adicioná-lo.
    </p>

    <div
        class="mt-2 rounded-lg border border-slate-300 bg-white p-3 shadow-sm transition
               focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500
               dark:border-neutral-700 dark:bg-neutral-950">
        <div x-cloak x-show="palavrasChave.length" class="mb-3 flex flex-wrap gap-2">
            <template x-for="palavra in palavrasChave" :key="palavra.id">
                <span
                    class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1.5
                           text-sm font-medium text-indigo-700
                           dark:bg-indigo-500/10 dark:text-indigo-300">
                    <span x-text="palavra.valor"></span>

                    <button type="button" x-on:click="removerPalavraChave(palavra.id)"
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full
                               text-indigo-500 transition hover:bg-indigo-100 hover:text-indigo-800
                               dark:text-indigo-400 dark:hover:bg-indigo-500/20 dark:hover:text-indigo-200"
                        aria-label="Remover palavra-chave" title="Remover palavra-chave">
                        <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                    </button>

                    <input type="hidden" name="palavras_chave[]" :value="palavra.valor">
                </span>
            </template>
        </div>

        <div class="flex items-center gap-3">
            <input id="nova_palavra_chave" type="text" x-model="novaPalavra"
                :name="novaPalavra.trim() ? 'palavras_chave[]' : null"
                x-on:keydown.enter.prevent="adicionarPalavraChave" maxlength="100" placeholder="Ex.: saúde pública"
                class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-950
                       shadow-none placeholder:text-slate-400 focus:border-0 focus:ring-0
                       dark:text-neutral-100 dark:placeholder:text-neutral-600">

            <button type="button" x-on:click="adicionarPalavraChave"
                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                       bg-indigo-600 text-white transition hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                       dark:focus:ring-offset-neutral-950"
                aria-label="Adicionar palavra-chave" title="Adicionar palavra-chave">
                <i class="fa-solid fa-plus text-sm" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <x-input-error for="palavras_chave" class="mt-1.5 dark:text-red-400" />
    <x-input-error for="palavras_chave.*" class="mt-1.5 dark:text-red-400" />
</div>
