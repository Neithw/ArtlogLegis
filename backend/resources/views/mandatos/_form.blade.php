@php
    $mandato = $mandato ?? null;
    $edicao = $mandato !== null;
    $usuarioIsRoot = $usuarioIsRoot ?? false;
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    @if (!$edicao)
        <div class="md:col-span-2">
            <x-ui::select name="vereador_id" label="Vereador" x-model="vereadorId" x-on:change="alterarVereador" required>
                <option value="">Selecione um vereador</option>

                @foreach ($vereadores as $vereador)
                    <option value="{{ $vereador->id }}" @selected(old('vereador_id') == $vereador->id)>
                        {{ $vereador->nome_parlamentar ?? $vereador->nome }}

                        @if ($usuarioIsRoot)
                            – {{ $vereador->camara->nome }}
                        @endif
                    </option>
                @endforeach
            </x-ui::select>
        </div>

        <div class="md:col-span-2">
            <x-ui::select name="legislatura_id" label="Legislatura" x-model="legislaturaId" x-bind:disabled="!vereadorId"
                required>
                <option value="">Selecione uma legislatura</option>

                <template x-for="legislatura in legislaturasFiltradas" x-bind:key="legislatura.id">
                    <option x-bind:value="String(legislatura.id)"
                        x-bind:selected="String(legislatura.id) === String(legislaturaId)" x-text="legislatura.rotulo">
                    </option>
                </template>
            </x-ui::select>

            <p x-show="!vereadorId" x-cloak class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
                Selecione um vereador para visualizar as legislaturas disponíveis.
            </p>

            <p x-show="vereadorId && legislaturasFiltradas.length === 0" x-cloak
                class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
                Nenhuma legislatura está disponível para a Câmara deste vereador.
            </p>
        </div>

        <div class="md:col-span-2">
            <x-ui::select name="partido_id" label="Partido inicial" required>
                <option value="">Selecione um partido</option>

                @foreach ($partidos as $partido)
                    <option value="{{ $partido->id }}" @selected(old('partido_id') == $partido->id)>
                        {{ $partido->sigla }} – {{ $partido->nome }}
                    </option>
                @endforeach
            </x-ui::select>
        </div>
    @else
        <div>
            <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                Vereador
            </p>

            <div
                class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                       dark:border-neutral-800 dark:bg-neutral-950">
                <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                    {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                </p>

                <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                    {{ $mandato->vereador->nome }}
                </p>
            </div>
        </div>

        <div>
            <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                Legislatura
            </p>

            <div
                class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                       dark:border-neutral-800 dark:bg-neutral-950">
                <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                    {{ $mandato->legislatura->numero }}ª Legislatura
                </p>

                <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                    {{ $mandato->legislatura->camara->nome }}
                </p>
            </div>
        </div>

        <div class="md:col-span-2">
            <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                Partido atual
            </p>

            <div
                class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                       dark:border-neutral-800 dark:bg-neutral-950">
                <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                    @if ($mandato->ultimaFiliacaoPartidaria?->partido)
                        {{ $mandato->ultimaFiliacaoPartidaria->partido->sigla }}
                        –
                        {{ $mandato->ultimaFiliacaoPartidaria->partido->nome }}
                    @else
                        Sem partido informado
                    @endif
                </p>

                <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                    Trocas partidárias são registradas separadamente para preservar o histórico.
                </p>
            </div>
        </div>
    @endif

    <x-ui::input name="data_inicio" label="Data de início" type="date" :value="$mandato?->data_inicio?->format('Y-m-d')" required />

    <x-ui::input name="data_fim" label="Data de término" type="date" :value="$mandato?->data_fim?->format('Y-m-d')"
        hint="Deixe em branco enquanto o mandato estiver em andamento." />
</div>
