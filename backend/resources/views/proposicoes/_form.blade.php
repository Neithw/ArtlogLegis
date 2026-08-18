@php
    $editando = isset($proposicao);
    $usuarioIsRoot = auth()->user()->isRoot();

    $hoje = today();

    $camaraIdInicial = (string) old(
        'camara_id',
        $editando ? $proposicao->camara_id : ($usuarioIsRoot ? '' : auth()->user()->camara_id),
    );

    $legislaturaIdInicial = (string) old('legislatura_id', $editando ? $proposicao->legislatura_id : '');

    $tipoProposicaoIdInicial = (string) old('tipo_proposicao_id', $editando ? $proposicao->tipo_proposicao_id : '');

    $autorMandatoIdInicial = (string) old('autor_mandato_id', $editando ? $proposicao->autor_mandato_id : '');

    $legislaturasAlpine = $legislaturas
        ->map(
            fn($legislatura) => [
                'id' => $legislatura->id,
                'numero' => $legislatura->numero,
                'camara_id' => $legislatura->camara_id,
                'arquivada' => $legislatura->trashed(),
            ],
        )
        ->values();

    $tiposProposicaoAlpine = $tiposProposicao
        ->map(
            fn($tipo) => [
                'id' => $tipo->id,
                'nome' => $tipo->nome,
                'camara_id' => $tipo->camara_id,
                'arquivado' => $tipo->trashed(),
            ],
        )
        ->values();

    $mandatosAlpine = $mandatos
        ->map(
            fn($mandato) => [
                'id' => $mandato->id,
                'camara_id' => $mandato->vereador->camara_id,
                'legislatura_id' => $mandato->legislatura_id,
                'nome' => $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome,

                'elegivel' => $mandato->estaVigenteEm($hoje),

                'situacao' => match (true) {
                    $mandato->trashed() => 'mandato arquivado',
                    $mandato->data_inicio->gt($hoje) => 'mandato futuro',

                    $mandato->data_fim && $mandato->data_fim->lt($hoje) => 'mandato encerrado',

                    default => null,
                },
            ],
        )
        ->values();
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2" x-data="{
    camaraId: @js($camaraIdInicial),
    legislaturaId: @js($legislaturaIdInicial),
    tipoProposicaoId: @js($tipoProposicaoIdInicial),
    autorMandatoId: @js($autorMandatoIdInicial),
    legislaturas: @js($legislaturasAlpine),
    tiposProposicao: @js($tiposProposicaoAlpine),
    mandatos: @js($mandatosAlpine),

    get legislaturasFiltradas() {
        return this.legislaturas.filter(
            legislatura => String(legislatura.camara_id) === String(this.camaraId)
        );
    },

    get tiposProposicaoFiltrados() {
        return this.tiposProposicao.filter(
            tipo => String(tipo.camara_id) === String(this.camaraId)
        );
    },

    get mandatosFiltrados() {
        return this.mandatos.filter(
            mandato =>
            String(mandato.camara_id) === String(this.camaraId) &&
            String(mandato.legislatura_id) === String(this.legislaturaId) &&
            (
                mandato.elegivel ||
                String(mandato.id) === String(this.autorMandatoId)
            )
        );
    },

    get autorMandatoSelecionado() {
        return this.mandatos.find(
            mandato =>
            String(mandato.id) === String(this.autorMandatoId)
        );
    },

    get autorMandatoIndisponivel() {
        return this.autorMandatoSelecionado &&
            !this.autorMandatoSelecionado.elegivel;
    },

    alterarCamara() {
        this.legislaturaId = '';
        this.tipoProposicaoId = '';
        this.autorMandatoId = '';
    },

    alterarLegislatura() {
        this.autorMandatoId = '';
    },
}">
    @if ($usuarioIsRoot)
        @if (!$editando)
            <div class="md:col-span-2">
                <x-ui::select name="camara_id" label="Câmara" x-model="camaraId" x-on:change="alterarCamara" required>
                    <option value="">Selecione uma Câmara</option>

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
                        {{ $proposicao->camara?->nome ?? 'Câmara indisponível' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                        A Câmara da proposição não pode ser alterada.
                    </p>
                </div>
            </div>
        @endif
    @endif

    <x-ui::select name="legislatura_id" label="Legislatura" x-model="legislaturaId" x-on:change="alterarLegislatura"
        x-bind:disabled="!camaraId" required>
        <option value="">Selecione uma legislatura</option>

        <template x-for="legislatura in legislaturasFiltradas" :key="legislatura.id">
            <option :value="String(legislatura.id)" :selected="String(legislatura.id) === String(legislaturaId)"
                x-text="`${legislatura.numero}ª Legislatura${legislatura.arquivada ? ' (arquivada)' : ''}`">
            </option>
        </template>
    </x-ui::select>

    <x-ui::select name="tipo_proposicao_id" label="Tipo de proposição" x-model="tipoProposicaoId"
        x-bind:disabled="!camaraId" required>
        <option value="">Selecione um tipo</option>

        <template x-for="tipo in tiposProposicaoFiltrados" :key="tipo.id">
            <option :value="String(tipo.id)" :selected="String(tipo.id) === String(tipoProposicaoId)"
                x-text="`${tipo.nome}${tipo.arquivado ? ' (arquivado)' : ''}`">
            </option>
        </template>
    </x-ui::select>

    <div class="md:col-span-2">
        <x-ui::select name="autor_mandato_id" label="Autor principal" x-model="autorMandatoId"
            x-bind:disabled="!legislaturaId" required>
            <option value="">Selecione um autor</option>

            <template x-for="mandato in mandatosFiltrados" :key="mandato.id">
                <option :value="String(mandato.id)" :selected="String(mandato.id) === String(autorMandatoId)"
                    x-text="mandato.nome + (mandato.situacao ? ` (${mandato.situacao})` : '')">
                </option>
            </template>
        </x-ui::select>

        <p x-cloak x-show="autorMandatoIndisponivel" class="mt-1.5 text-sm text-amber-700 dark:text-amber-300">

            O mandato atualmente vinculado não está vigente.
            Selecione outro autor para salvar ou protocolar a proposição.
        </p>

        <p x-cloak x-show="legislaturaId && mandatosFiltrados.length === 0"
            class="mt-1.5 text-sm text-slate-500 dark:text-neutral-500">
            Nenhum mandato vigente disponível para esta legislatura.
        </p>
    </div>

    <div class="md:col-span-2">
        <x-ui::input name="ementa" label="Ementa" type="text" :value="$proposicao->ementa ?? null" />
    </div>

    <x-ui::input name="assunto" label="Assunto" type="text" :value="$proposicao->assunto ?? null" maxlength="255" />

    <x-ui::input name="area_tematica" label="Área temática" type="text" :value="$proposicao->area_tematica ?? null" maxlength="255" />

    <div class="md:col-span-2">
        <x-ui::textarea name="texto_integral" label="Texto integral" rows="12" :value="$proposicao->texto_integral ?? null" />
    </div>

    @include('proposicoes._palavras-chave')
</div>
