@php
    $partido = $partido ?? null;
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    <x-ui::input name="nome" label="Nome do partido" :value="$partido?->nome" required autofocus />

    <x-ui::input name="sigla" label="Sigla do partido" :value="$partido?->sigla" required />

    <x-ui::input name="numero_eleitoral" label="Número eleitoral do partido" type="number" :value="$partido?->numero_eleitoral"
        hint="Informe apenas o número, por exemplo: 20." min="1" max="65535" inputmode="numeric"
        class="md:max-w-sm" />
</div>
