@php
    $sessao = $sessao ?? null;
    $edicao = $sessao !== null;
    $usuarioIsRoot = auth()->user()->isRoot();

    $usarFiltroCamara = $usuarioIsRoot && !$edicao;

    $camaraSelecionada = (string) old('camara_id', '');
    $legislaturaSelecionada = (string) old('legislatura_id', $sessao?->legislatura_id ?? '');

    $legislaturasParaAlpine = $usarFiltroCamara
        ? $legislaturas
            ->map(
                fn($legislatura) => [
                    'id' => (string) $legislatura->id,
                    'numero' => $legislatura->numero,
                    'camara_id' => (string) $legislatura->camara_id,
                ],
            )
            ->values()
        : collect();
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2"
    @if ($usarFiltroCamara) x-data="{
        camaraSelecionada: @js($camaraSelecionada),
        legislaturaSelecionada: @js($legislaturaSelecionada),
        legislaturas: @js($legislaturasParaAlpine),

        get legislaturasFiltradas() {
            if (! this.camaraSelecionada) {
                return [];
            }

            return this.legislaturas.filter(
                legislatura => legislatura.camara_id === this.camaraSelecionada
            );
        }
    }" @endif>
    @if ($usuarioIsRoot)
        @if (!$edicao)
            <div class="md:col-span-2">
                <x-ui::select name="camara_id" label="Câmara" x-model="camaraSelecionada"
                    x-on:change="legislaturaSelecionada = ''" required>
                    <option value="">
                        Selecione uma Câmara
                    </option>

                    @foreach ($camaras as $camara)
                        <option value="{{ $camara->id }}" @selected(old('camara_id') == $camara->id)>
                            {{ $camara->nome }}
                        </option>
                    @endforeach
                </x-ui::select>
            </div>
        @else
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                    Câmara
                </p>

                <div
                    class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                       dark:border-neutral-800 dark:bg-neutral-950">
                    <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                        {{ $sessao->camara->nome }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                        A Câmara da sessão não pode ser alterada após o cadastro.
                    </p>
                </div>
            </div>
        @endif
    @endif

    <div>
        @if ($usarFiltroCamara)
            <x-ui::select name="legislatura_id" label="Legislatura" x-model="legislaturaSelecionada"
                x-bind:disabled="!camaraSelecionada" required>
                <option value="">
                    Selecione uma legislatura
                </option>

                <template x-for="legislatura in legislaturasFiltradas" :key="legislatura.id">
                    <option :value="legislatura.id" x-text="`${legislatura.numero}ª Legislatura`"></option>
                </template>
            </x-ui::select>
        @else
            <x-ui::select name="legislatura_id" label="Legislatura" required>
                <option value="">
                    Selecione uma legislatura
                </option>

                @foreach ($legislaturas as $legislatura)
                    <option value="{{ $legislatura->id }}" @selected(old('legislatura_id', $sessao?->legislatura_id) == $legislatura->id)>
                        {{ $legislatura->numero }}ª Legislatura
                    </option>
                @endforeach
            </x-ui::select>
        @endif
    </div>

    <div>
        <x-ui::select name="tipo" label="Tipo" required>
            <option value="">
                Selecione um tipo
            </option>

            @foreach ($tipos as $valor => $label)
                <option value="{{ $valor }}" @selected(old('tipo', $sessao?->tipo) === $valor)>
                    {{ $label }}
                </option>
            @endforeach
        </x-ui::select>
    </div>

    <x-ui::input name="data_hora_inicio_previsto" label="Data e horário de início" type="datetime-local"
        :value="$sessao?->data_hora_inicio_previsto?->format('Y-m-d\TH:i')" required />

    <x-ui::input name="local" label="Local" type="text" :value="$sessao?->local" />
</div>
