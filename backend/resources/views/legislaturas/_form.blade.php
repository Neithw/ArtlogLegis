@php
    $legislatura = $legislatura ?? null;
    $edicao = $legislatura !== null;
    $usuarioIsRoot = auth()->user()->isRoot();
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    @if ($usuarioIsRoot)
        @if (!$edicao)
            <div class="md:col-span-2">
                <x-ui::select name="camara_id" label="Câmara" required>
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
                        {{ $legislatura->camara->nome }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                        A Câmara da legislatura não pode ser alterada após o cadastro.
                    </p>
                </div>
            </div>
        @endif
    @endif

    <div class="md:col-span-2">
        <x-ui::input name="numero" label="Número da legislatura" type="number" :value="$legislatura?->numero"
            hint="Informe apenas o número, por exemplo: 20." min="1" max="65535" inputmode="numeric" required
            autofocus />
    </div>

    <x-ui::input name="data_inicio" label="Data de início" type="date" :value="$legislatura?->data_inicio?->format('Y-m-d')" required />

    <x-ui::input name="data_fim" label="Data de término" type="date" :value="$legislatura?->data_fim?->format('Y-m-d')" required />
</div>
