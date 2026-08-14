@php
    $editando = isset($tipoProposicao);

    $usuarioIsRoot = auth()->user()->isRoot();
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    @if ($usuarioIsRoot)
        @if (!$editando)
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
                        {{ $tipoProposicao->camara?->nome ?? 'Câmara indisponível' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                        A Câmara do tipo de proposição não pode ser alterada após o cadastro.
                    </p>
                </div>

                <x-input-error for="camara_id" class="mt-1.5 dark:text-red-400" />
            </div>
        @endif
    @endif

    <div class="md:col-span-2">
        <x-ui::input name="nome" label="Nome do tipo" type="text" :value="old('nome', $tipoProposicao->nome ?? '')" maxlength="255" required
            autofocus />
    </div>
</div>
