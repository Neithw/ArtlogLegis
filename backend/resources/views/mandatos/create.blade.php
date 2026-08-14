<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Novo mandato
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @php
                $configuracaoFormulario = [
                    'vereadorId' => (string) old('vereador_id', ''),
                    'legislaturaId' => (string) old('legislatura_id', ''),

                    'vereadores' => $vereadores
                        ->map(
                            fn($vereador) => [
                                'id' => $vereador->id,
                                'camara_id' => $vereador->camara_id,
                            ],
                        )
                        ->values(),

                    'legislaturas' => $legislaturas
                        ->map(
                            fn($legislatura) => [
                                'id' => $legislatura->id,
                                'camara_id' => $legislatura->camara_id,
                                'rotulo' =>
                                    $legislatura->numero .
                                    'ª Legislatura' .
                                    ($usuarioIsRoot ? ' – ' . $legislatura->camara->nome : ''),
                            ],
                        )
                        ->values(),
                ];
            @endphp

            <form action="{{ route('mandatos.store') }}" method="POST" class="space-y-6" x-data="formularioMandato({{ Js::from($configuracaoFormulario) }})">
                @csrf

                <x-ui::card>
                    <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Dados do mandato
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Informe o vereador, a legislatura, o partido inicial e o período.
                        </p>
                    </header>

                    @include('mandatos._form', ['mandato' => null])
                </x-ui::card>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui::button :href="route('mandatos.index')" variant="secondary">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Cancelar
                    </x-ui::button>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Cadastrar mandato
                    </x-ui::button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
