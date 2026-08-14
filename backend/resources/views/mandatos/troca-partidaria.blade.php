<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Troca partidária
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form action="{{ route('mandatos.troca-partidaria.store', $mandato) }}" method="POST" class="space-y-6">
                @csrf

                <x-ui::card>
                    <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Registrar troca partidária
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Encerre a filiação atual e registre o novo partido.
                        </p>
                    </header>

                    <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
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
                                Filiação atual
                            </p>

                            <div
                                class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                                       dark:border-neutral-800 dark:bg-neutral-950">
                                <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $filiacaoAtual->partido->sigla }}
                                    –
                                    {{ $filiacaoAtual->partido->nome }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                                    Filiação iniciada em
                                    {{ $filiacaoAtual->data_inicio->format('d/m/Y') }}.
                                </p>
                            </div>
                        </div>

                        <x-ui::select name="partido_id" label="Novo partido" required>
                            <option value="">Selecione um partido</option>

                            @foreach ($partidos as $partido)
                                <option value="{{ $partido->id }}" @selected(old('partido_id') == $partido->id)>
                                    {{ $partido->sigla }} – {{ $partido->nome }}
                                </option>
                            @endforeach
                        </x-ui::select>

                        <x-ui::input name="data_troca" label="Data da troca" type="date"
                            hint="A data deve estar dentro do período do mandato." required />
                    </div>
                </x-ui::card>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui::button :href="route('mandatos.show', $mandato)" variant="secondary">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Cancelar
                    </x-ui::button>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                        Registrar troca
                    </x-ui::button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
