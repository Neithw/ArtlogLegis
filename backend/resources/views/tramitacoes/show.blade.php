<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold text-slate-950 dark:text-neutral-100">
                Tramitação da proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
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

            @error('proposicao')
                <x-ui::alert type="error">
                    {{ $message }}
                </x-ui::alert>
            @enderror

            @error('tramitacao')
                <x-ui::alert type="error">
                    {{ $message }}
                </x-ui::alert>
            @enderror

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                            <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                    @if ($proposicao->situacao === 'protocolada')
                                        {{ $proposicao->tipoProposicao->nome }} nº
                                        {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                    @else
                                        Rascunho #{{ $proposicao->id }}
                                    @endif
                                </h3>

                                <span @class([
                                    'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' =>
                                        $proposicao->situacao === 'rascunho',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' =>
                                        $proposicao->situacao === 'protocolada',
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $proposicao->situacao)) }}
                                </span>
                            </div>

                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-neutral-300">
                                {{ $proposicao->ementa }}
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('proposicoes.show', $proposicao) }}"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:ring-offset-neutral-900">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Voltar à proposição
                    </a>
                </div>

                <dl
                    class="grid gap-px border-t border-slate-200 bg-slate-200 sm:grid-cols-2 lg:grid-cols-4 dark:border-neutral-800 dark:bg-neutral-800">
                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                            Câmara
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $proposicao->camara->nome }}
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                            Legislatura
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $proposicao->legislatura->numero }}ª Legislatura
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                            Autor principal
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $proposicao->autorMandato->vereador->nome_parlamentar ?: $proposicao->autorMandato->vereador->nome }}
                        </dd>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                            Registros
                        </dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                            {{ $proposicao->tramitacoes->count() }}
                            {{ $proposicao->tramitacoes->count() === 1 ? 'tramitação' : 'tramitações' }}
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($proposicao->situacao === 'protocolada')
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3
                                    class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                                    <i class="fa-solid fa-location-dot text-slate-400 dark:text-neutral-500"
                                        aria-hidden="true"></i>
                                    Situação atual
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Localização e estado do encaminhamento mais recente.
                                </p>
                            </div>

                            @if ($tramitacaoPendente)
                                <span
                                    class="inline-flex self-start items-center gap-2 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                    <i class="fa-solid fa-clock" aria-hidden="true"></i>
                                    Aguardando recebimento
                                </span>
                            @elseif ($unidadeAtual)
                                <span
                                    class="inline-flex self-start items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                                    Recebida
                                </span>
                            @else
                                <span
                                    class="inline-flex self-start items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                                    <i class="fa-solid fa-circle-pause" aria-hidden="true"></i>
                                    Não iniciada
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($tramitacaoPendente)
                            <div class="grid gap-4 md:grid-cols-[1fr_auto_1fr] md:items-center">
                                <div class="rounded-xl bg-slate-50 p-4 dark:bg-neutral-950">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                                        Localização atual
                                    </p>
                                    <p
                                        class="mt-2 flex items-center gap-2 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                        <i class="fa-solid fa-building text-slate-400" aria-hidden="true"></i>
                                        {{ $tramitacaoPendente->unidadeOrigem?->nome ?? 'Protocolo' }}
                                    </p>
                                </div>

                                <div class="flex justify-center text-slate-400 md:px-2 dark:text-neutral-500">
                                    <i class="fa-solid fa-arrow-down md:hidden" aria-hidden="true"></i>
                                    <i class="fa-solid fa-arrow-right-long hidden md:inline" aria-hidden="true"></i>
                                </div>

                                <div
                                    class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/60 dark:bg-amber-950/30">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">
                                        Destino pendente
                                    </p>
                                    <p
                                        class="mt-2 flex items-center gap-2 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                        <i class="fa-solid fa-inbox text-amber-500" aria-hidden="true"></i>
                                        {{ $tramitacaoPendente->unidadeDestino->nome }}
                                    </p>
                                </div>
                            </div>

                            @can('receber', $tramitacaoPendente)
                                <div
                                    class="mt-6 flex flex-col gap-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-emerald-900/60 dark:bg-emerald-950/30">
                                    <div>
                                        <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-200">
                                            Esta proposição aguarda recebimento na sua unidade.
                                        </p>
                                        <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                            A confirmação tornará esta unidade a localização atual da proposição.
                                        </p>
                                    </div>

                                    <form action="{{ route('tramitacoes.receber', $tramitacaoPendente) }}" method="POST"
                                        onsubmit="return confirm('Deseja confirmar o recebimento desta proposição?');">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto dark:focus:ring-offset-neutral-900">
                                            <i class="fa-solid fa-check" aria-hidden="true"></i>
                                            Confirmar recebimento
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        @else
                            <div class="rounded-xl bg-slate-50 p-5 dark:bg-neutral-950">
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                                    Localização atual
                                </p>
                                <p
                                    class="mt-2 flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                                    <i class="fa-solid fa-building-columns text-slate-400 dark:text-neutral-500"
                                        aria-hidden="true"></i>
                                    {{ $unidadeAtual?->nome ?? 'Protocolo' }}
                                </p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    {{ $unidadeAtual
                                        ? 'A proposição foi recebida e está disponível para novo encaminhamento.'
                                        : 'A proposição ainda não recebeu seu primeiro encaminhamento.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </section>

                @if (!$tramitacaoPendente)
                    @can('encaminhar', $proposicao)
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                                <h3
                                    class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                                    <i class="fa-solid fa-paper-plane text-slate-400 dark:text-neutral-500"
                                        aria-hidden="true"></i>
                                    Novo encaminhamento
                                </h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    Encaminhe a proposição de
                                    <span class="font-medium text-slate-700 dark:text-neutral-300">
                                        {{ $unidadeAtual?->nome ?? 'Protocolo' }}
                                    </span>
                                    para outra unidade da Câmara.
                                </p>
                            </div>

                            <div class="p-6">
                                @if ($unidadesDestino->isEmpty())
                                    <div
                                        class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-300">
                                        <i class="fa-solid fa-circle-info mt-0.5 text-slate-400" aria-hidden="true"></i>
                                        <p>Não há outra unidade disponível para encaminhamento.</p>
                                    </div>
                                @else
                                    <form action="{{ route('proposicoes.tramitacoes.store', $proposicao) }}" method="POST"
                                        class="space-y-5">
                                        @csrf

                                        <div>
                                            <label for="unidade_destino_id"
                                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                                Unidade de destino
                                            </label>

                                            <select id="unidade_destino_id" name="unidade_destino_id" required
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100">
                                                <option value="">Selecione uma unidade</option>

                                                @foreach ($unidadesDestino as $unidade)
                                                    <option value="{{ $unidade->id }}" @selected(old('unidade_destino_id') == $unidade->id)>
                                                        {{ $unidade->nome }}
                                                        @if ($unidade->sigla)
                                                            ({{ $unidade->sigla }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>

                                            <x-input-error for="unidade_destino_id" class="mt-2 dark:text-red-400" />
                                        </div>

                                        <div>
                                            <div class="flex items-center justify-between gap-4">
                                                <label for="despacho"
                                                    class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                                    Despacho
                                                </label>
                                                <span class="text-xs text-slate-400">Opcional</span>
                                            </div>

                                            <textarea id="despacho" name="despacho" rows="4" maxlength="5000"
                                                placeholder="Informe uma orientação ou observação para a unidade de destino."
                                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600">{{ old('despacho') }}</textarea>

                                            <x-input-error for="despacho" class="mt-2 dark:text-red-400" />
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto dark:focus:ring-offset-neutral-900">
                                                <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                                                Encaminhar proposição
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </section>
                    @endcan
                @endif

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div
                        class="flex flex-col gap-2 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-neutral-800">
                        <div>
                            <h3
                                class="flex items-center gap-2 text-base font-semibold text-slate-950 dark:text-neutral-100">
                                <i class="fa-solid fa-timeline text-slate-400 dark:text-neutral-500"
                                    aria-hidden="true"></i>
                                Histórico da tramitação
                            </h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Registros mais recentes primeiro.
                            </p>
                        </div>

                        <span
                            class="inline-flex self-start items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-neutral-800 dark:text-neutral-300">
                            {{ $proposicao->tramitacoes->count() }}
                            {{ $proposicao->tramitacoes->count() === 1 ? 'registro' : 'registros' }}
                        </span>
                    </div>

                    <div class="p-6">
                        @forelse ($proposicao->tramitacoes as $tramitacao)
                            <article class="relative pb-8 pl-10 last:pb-0">
                                @if (!$loop->last)
                                    <span
                                        class="absolute left-[0.6875rem] top-7 h-[calc(100%-0.5rem)] w-px bg-slate-200 dark:bg-neutral-700"
                                        aria-hidden="true"></span>
                                @endif

                                <span @class([
                                    'absolute left-0 top-0 flex h-6 w-6 items-center justify-center rounded-full ring-4 ring-white dark:ring-neutral-900',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' =>
                                        $tramitacao->data_recebimento === null,
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' =>
                                        $tramitacao->data_recebimento !== null,
                                ])>
                                    <i @class([
                                        'fa-solid text-[10px]',
                                        'fa-clock' => $tramitacao->data_recebimento === null,
                                        'fa-check' => $tramitacao->data_recebimento !== null,
                                    ]) aria-hidden="true"></i>
                                </span>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-neutral-800">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p
                                                class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                                <span>{{ $tramitacao->unidadeOrigem?->nome ?? 'Protocolo' }}</span>
                                                <i class="fa-solid fa-arrow-right-long text-xs text-slate-400"
                                                    aria-hidden="true"></i>
                                                <span>{{ $tramitacao->unidadeDestino->nome }}</span>
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                                Encaminhada em
                                                {{ $tramitacao->data_encaminhamento->format('d/m/Y \à\s H:i') }}
                                                por {{ $tramitacao->encaminhadoPor->name }}
                                            </p>
                                        </div>

                                        <span @class([
                                            'inline-flex self-start items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold',
                                            'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' =>
                                                $tramitacao->data_recebimento === null,
                                            'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' =>
                                                $tramitacao->data_recebimento !== null,
                                        ])>
                                            <i @class([
                                                'fa-solid',
                                                'fa-clock' => $tramitacao->data_recebimento === null,
                                                'fa-circle-check' => $tramitacao->data_recebimento !== null,
                                            ]) aria-hidden="true"></i>
                                            {{ $tramitacao->data_recebimento === null ? 'Pendente' : 'Recebida' }}
                                        </span>
                                    </div>

                                    @if ($tramitacao->despacho)
                                        <div class="mt-4 rounded-lg bg-slate-50 px-4 py-3 dark:bg-neutral-950">
                                            <p
                                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-400">
                                                Despacho
                                            </p>
                                            <p
                                                class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700 dark:text-neutral-300">
                                                {{ $tramitacao->despacho }}</p>
                                        </div>
                                    @endif

                                    @if ($tramitacao->data_recebimento)
                                        <p
                                            class="mt-4 flex items-center gap-2 text-xs text-slate-500 dark:text-neutral-400">
                                            <i class="fa-solid fa-inbox text-emerald-500" aria-hidden="true"></i>
                                            Recebida em
                                            {{ $tramitacao->data_recebimento->format('d/m/Y \à\s H:i') }}
                                            por {{ $tramitacao->recebidoPor->name }}
                                        </p>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="py-8 text-center">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-neutral-800 dark:text-neutral-500">
                                    <i class="fa-solid fa-route" aria-hidden="true"></i>
                                </div>
                                <p class="mt-4 text-sm font-medium text-slate-700 dark:text-neutral-300">
                                    Nenhuma tramitação registrada
                                </p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                    O primeiro encaminhamento aparecerá aqui.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
