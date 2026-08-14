<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes do mandato
            </h2>
        </div>
    </x-slot>

    @php
        $hoje = now()->startOfDay();

        if ($hoje->lt($mandato->data_inicio)) {
            $situacao = 'Futuro';
            $variante = 'info';
        } elseif ($mandato->data_fim && $hoje->gt($mandato->data_fim)) {
            $situacao = 'Encerrado';
            $variante = 'neutral';
        } else {
            $situacao = 'Em andamento';
            $variante = 'success';
        }

        $filiacoes = $mandato->filiacoesPartidarias;

        $filiacaoVigente = $filiacoes->first(
            fn($filiacao) => $filiacao->data_inicio->lte($hoje) &&
                (!$filiacao->data_fim || $filiacao->data_fim->gte($hoje)),
        );

        if ($situacao === 'Futuro') {
            $rotuloPartido = 'Partido inicial';
            $filiacaoDestaque = $filiacoes->first();
        } elseif ($situacao === 'Encerrado') {
            $rotuloPartido = 'Último partido';
            $filiacaoDestaque = $filiacoes->last();
        } else {
            $rotuloPartido = 'Partido atual';
            $filiacaoDestaque = $filiacaoVigente;
        }
    @endphp

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui::alert>
                    {{ session('success') }}
                </x-ui::alert>
            @endif

            @if (session('error'))
                <x-ui::alert type="error">
                    {{ session('error') }}
                </x-ui::alert>
            @endif

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $mandato->vereador->nome_parlamentar ?? $mandato->vereador->nome }}
                            </h3>

                            <x-ui::badge :variant="$variante">
                                {{ $situacao }}
                            </x-ui::badge>
                        </div>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{ $mandato->legislatura->numero }}ª Legislatura
                            <span aria-hidden="true">·</span>
                            {{ $mandato->legislatura->camara->nome }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui::button :href="route('mandatos.index')" variant="secondary">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Voltar
                        </x-ui::button>

                        @can('update', $mandato)
                            <x-ui::button :href="route('mandatos.edit', $mandato)" variant="secondary">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                Editar
                            </x-ui::button>

                            <x-ui::button :href="route('mandatos.troca-partidaria.create', $mandato)">
                                <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                                Trocar partido
                            </x-ui::button>
                        @endcan
                    </div>
                </header>

                <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Vereador
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $mandato->vereador->nome }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Legislatura
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            {{ $mandato->legislatura->numero }}ª Legislatura
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Data de início
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $mandato->data_inicio->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Data de término
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $mandato->data_fim?->format('d/m/Y') ?? 'Em aberto' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            {{ $rotuloPartido }}
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                            @if ($filiacaoDestaque?->partido)
                                {{ $filiacaoDestaque->partido->sigla }}
                                –
                                {{ $filiacaoDestaque->partido->nome }}
                            @else
                                Sem filiação partidária registrada
                            @endif
                        </p>
                    </div>
                </div>
            </x-ui::card>

            <x-ui::card>
                <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                        Histórico partidário
                    </h3>

                    <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                        Filiações registradas durante este mandato.
                    </p>
                </header>

                <x-ui::table>
                    <thead>
                        <tr>
                            <th scope="col">Partido</th>
                            <th scope="col">Início</th>
                            <th scope="col">Término</th>
                            <th scope="col">Situação</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($mandato->filiacoesPartidarias as $filiacao)
                            @php
                                if ($filiacao->data_inicio->gt($hoje)) {
                                    $situacaoFiliacao = 'Futura';
                                    $varianteFiliacao = 'info';
                                } elseif (!$filiacao->data_fim || $filiacao->data_fim->gte($hoje)) {
                                    $situacaoFiliacao = 'Vigente';
                                    $varianteFiliacao = 'success';
                                } else {
                                    $situacaoFiliacao = 'Encerrada';
                                    $varianteFiliacao = 'neutral';
                                }
                            @endphp

                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-neutral-800/50">
                                <td>
                                    <div>
                                        <p class="font-semibold text-slate-950 dark:text-neutral-100">
                                            {{ $filiacao->partido->sigla }}
                                        </p>

                                        <p class="text-xs text-slate-500 dark:text-neutral-500">
                                            {{ $filiacao->partido->nome }}
                                        </p>
                                    </div>
                                </td>

                                <td>
                                    {{ $filiacao->data_inicio->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $filiacao->data_fim?->format('d/m/Y') ?? 'Em aberto' }}
                                </td>

                                <td>
                                    <x-ui::badge :variant="$varianteFiliacao">
                                        {{ $situacaoFiliacao }}
                                    </x-ui::badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui::empty-state icon="fa-flag">
                                        Nenhuma filiação partidária registrada.
                                    </x-ui::empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui::table>
            </x-ui::card>

            @can('delete', $mandato)
                <div class="flex justify-end">
                    <form action="{{ route('mandatos.destroy', $mandato) }}" method="POST"
                        onsubmit="return confirm('Deseja realmente arquivar este mandato?')">
                        @csrf
                        @method('DELETE')

                        <x-ui::button type="submit" variant="danger">
                            <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                            Arquivar mandato
                        </x-ui::button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
