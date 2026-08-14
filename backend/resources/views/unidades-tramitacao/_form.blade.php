@php
    $unidadeTramitacao = $unidadeTramitacao ?? null;
    $edicao = $unidadeTramitacao !== null;
    $usuarioIsRoot = auth()->user()->isRoot();
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    @if ($usuarioIsRoot)
        @if (!$edicao)
            <div class="md:col-span-2">
                <x-ui::select name="camara_id" label="Câmara" required>
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
                        {{ $unidadeTramitacao->camara?->nome ?? 'Câmara não encontrada' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                        A Câmara da unidade não pode ser alterada após o cadastro.
                    </p>
                </div>

                <x-input-error for="camara_id" class="mt-1.5 dark:text-red-400" />
            </div>
        @endif
    @endif

    <x-ui::input name="nome" label="Nome da unidade" :value="$unidadeTramitacao?->nome" maxlength="255" required />

    <x-ui::input name="sigla" label="Sigla" :value="$unidadeTramitacao?->sigla" maxlength="50"
        hint="A sigla será armazenada em letras maiúsculas." />

    <div class="md:col-span-2">
        <x-ui::select name="tipo" label="Tipo" required>
            <option value="">Selecione um tipo</option>

            @foreach ($tiposLabels as $valor => $label)
                <option value="{{ $valor }}" @selected(old('tipo', $unidadeTramitacao?->tipo) === $valor)>
                    {{ $label }}
                </option>
            @endforeach
        </x-ui::select>
    </div>

    <div class="md:col-span-2">
        <x-ui::textarea name="descricao" label="Descrição" :value="$unidadeTramitacao?->descricao" rows="4"
            hint="Descreva brevemente a função desta unidade no fluxo legislativo." />
    </div>
</div>
