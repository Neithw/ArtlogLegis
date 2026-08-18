@php
    $usuarioIsRoot = auth()->user()->isRoot();
    $autor = $proposicao->autorMandato?->vereador;
    $nomeAutor = $autor?->nome_parlamentar ?: $autor?->nome ?? 'Autor indisponível';
    $tipoProposicao = $proposicao->tipoProposicao?->nome ?? 'Proposição';

    $titulo =
        $proposicao->numero !== null && $proposicao->ano !== null
            ? $tipoProposicao . ' nº ' . $proposicao->numero . '/' . $proposicao->ano
            : 'Rascunho #' . $proposicao->id;

    [$situacao, $variante, $iconeSituacao, $descricaoSituacao] = match ($proposicao->situacao) {
        'rascunho' => ['Rascunho', 'warning', 'fa-pen-ruler', 'Proposição em elaboração, ainda sem numeração oficial.'],
        'protocolada' => [
            'Protocolada',
            'success',
            'fa-circle-check',
            'Proposição protocolada e disponível para tramitação legislativa.',
        ],
        default => [
            ucfirst(str_replace('_', ' ', $proposicao->situacao)),
            'info',
            'fa-circle-info',
            'Registro preservado para acompanhamento do processo legislativo.',
        ],
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Processo legislativo
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Detalhes da proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
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

            @error('protocolo')
                <x-ui::alert type="error">
                    {{ $message }}
                </x-ui::alert>
            @enderror

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

            @if ($proposicao->situacao === 'rascunho' && !$autorMandatoVigente)
                <x-ui::alert type="error">
                    Esta proposição não pode ser protocolada porque o autor principal
                    não possui mandato vigente. Edite o rascunho e selecione um autor elegível.
                </x-ui::alert>
            @endif

            <div class="space-y-6">
                {{-- Resumo principal --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">

                    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 items-start gap-4">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                   bg-slate-100 text-slate-500
                                   dark:bg-neutral-800 dark:text-neutral-400">
                                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                        {{ $titulo }}
                                    </h3>

                                    <x-ui::badge :variant="$variante">
                                        <i class="fa-solid {{ $iconeSituacao }} w-3 shrink-0
                                               text-center leading-none"
                                            aria-hidden="true"></i>

                                        {{ $situacao }}
                                    </x-ui::badge>
                                </div>

                                <p
                                    class="mt-2 max-w-3xl text-sm leading-6
                                       text-slate-600 dark:text-neutral-300">
                                    <span class="block">
                                        {{ $tipoProposicao }} na
                                        {{ $proposicao->legislatura?->numero }}ª Legislatura.
                                    </span>

                                    <span class="block">
                                        {{ $descricaoSituacao }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
                            {{-- Navegação --}}
                            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <x-ui::button :href="route('proposicoes.index')" variant="secondary">
                                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                                    Voltar
                                </x-ui::button>
                            </div>

                            {{-- Ações operacionais --}}
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                @if ($proposicao->situacao === 'rascunho')
                                    @can('update', $proposicao)
                                        <x-ui::button :href="route('proposicoes.edit', $proposicao)" variant="edit">
                                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                            Editar
                                        </x-ui::button>
                                    @endcan
                                @endif

                                @if ($proposicao->situacao === 'rascunho')
                                    @if ($autorMandatoVigente)
                                        @can('protocolar', $proposicao)
                                            <form action="{{ route('proposicoes.protocolar', $proposicao) }}"
                                                method="POST"
                                                onsubmit="return confirm('Deseja realmente protocolar esta proposição? Esta ação não poderá ser desfeita.');">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui::button type="submit">
                                                    <i class="fa-solid fa-stamp" aria-hidden="true"></i>
                                                    Protocolar
                                                </x-ui::button>
                                            </form>
                                        @endcan
                                    @endif

                                    @can('delete', $proposicao)
                                        <form action="{{ route('proposicoes.destroy', $proposicao) }}" method="POST"
                                            onsubmit="return confirm('Deseja realmente arquivar esta proposição?');">
                                            @csrf
                                            @method('DELETE')

                                            <x-ui::button type="submit" variant="danger">
                                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                                Arquivar
                                            </x-ui::button>
                                        </form>
                                    @endcan
                                @endif

                                @if ($proposicao->situacao === 'protocolada')
                                    @can('viewAny', \App\Models\Tramitacao::class)
                                        <x-ui::button :href="route('proposicoes.tramitacao.show', $proposicao)">
                                            <i class="fa-solid fa-route" aria-hidden="true"></i>
                                            Tramitação
                                        </x-ui::button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </div>

                    <dl
                        class="grid gap-px border-t border-slate-200 bg-slate-200
                           sm:grid-cols-2 lg:grid-cols-4
                           dark:border-neutral-800 dark:bg-neutral-800">

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Tipo
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $tipoProposicao }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Legislatura
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->legislatura?->numero }}ª Legislatura
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Autor principal
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $nomeAutor }}
                            </dd>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Protocolo
                            </dt>

                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                @if ($proposicao->numero !== null && $proposicao->ano !== null)
                                    {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                @else
                                    Aguardando
                                @endif
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Informações institucionais --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold
                               text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-circle-info text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>

                            Informações da proposição
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Autoria, vínculo institucional e informações de registro.
                        </p>
                    </div>

                    <dl class="grid gap-6 p-6 md:grid-cols-2">
                        @if ($usuarioIsRoot)
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                    Câmara
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->camara?->nome ?? 'Câmara indisponível' }}
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Autor principal
                            </dt>

                            <dd class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $nomeAutor }}
                                </span>

                                @if ($proposicao->autorMandato?->trashed())
                                    <x-ui::badge variant="danger">
                                        <i class="fa-solid fa-box-archive w-3 shrink-0
                                               text-center leading-none"
                                            aria-hidden="true"></i>
                                        Mandato arquivado
                                    </x-ui::badge>
                                @elseif ($proposicao->situacao === 'rascunho' && !$autorMandatoVigente)
                                    <x-ui::badge variant="warning">
                                        <i class="fa-solid fa-clock w-3 shrink-0
                                               text-center leading-none"
                                            aria-hidden="true"></i>
                                        Mandato não vigente
                                    </x-ui::badge>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Criada por
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->criadoPor?->name ?? 'Usuário indisponível' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Cadastrada em
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->created_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                Última atualização
                            </dt>

                            <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->updated_at->format('d/m/Y \à\s H:i') }}
                            </dd>
                        </div>
                    </dl>
                </section>

                {{-- Conteúdo --}}
                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">

                    <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                        <h3
                            class="flex items-center gap-2 text-base font-semibold
                               text-slate-950 dark:text-neutral-100">
                            <i class="fa-solid fa-align-left text-slate-400 dark:text-neutral-500"
                                aria-hidden="true"></i>

                            Conteúdo da proposição
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Ementa, classificação temática, palavras-chave e texto integral.
                        </p>
                    </div>

                    <div class="space-y-6 p-6">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Ementa
                            </p>

                            <p class="mt-2 text-sm leading-6 text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->ementa ?: 'Não informada' }}
                            </p>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-neutral-400">
                                    Assunto
                                </p>

                                <p class="mt-2 text-sm text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->assunto ?: 'Não informado' }}
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-xs font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-neutral-400">
                                    Área temática
                                </p>

                                <p class="mt-2 text-sm text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->area_tematica ?: 'Não informada' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Palavras-chave
                            </p>

                            <div class="mt-2 flex flex-wrap gap-2">
                                @forelse ($proposicao->palavras_chave ?? [] as $palavraChave)
                                    <x-ui::badge>
                                        {{ $palavraChave }}
                                    </x-ui::badge>
                                @empty
                                    <span class="text-sm text-slate-500 dark:text-neutral-400">
                                        Nenhuma palavra-chave informada.
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                                Texto integral
                            </p>

                            <div
                                class="mt-2 rounded-xl border border-slate-200 bg-slate-50 p-4
                                   dark:border-neutral-800 dark:bg-neutral-950">
                                {{-- blade-formatter-disable-next-line --}}
                            <p class="whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-neutral-300">{{ $proposicao->texto_integral ?: 'Não informado' }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Protocolo --}}
                @if ($proposicao->situacao === 'protocolada')
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                           dark:border-neutral-800 dark:bg-neutral-900">

                        <div class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                            <h3
                                class="flex items-center gap-2 text-base font-semibold
                                   text-slate-950 dark:text-neutral-100">
                                <i class="fa-solid fa-stamp text-slate-400 dark:text-neutral-500"
                                    aria-hidden="true"></i>

                                Informações do protocolo
                            </h3>

                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Numeração oficial e responsabilidade pelo protocolo.
                            </p>
                        </div>

                        <dl class="grid gap-6 p-6 md:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                    Número
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                    Protocolada em
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    <time datetime="{{ $proposicao->data_protocolo?->toIso8601String() }}">
                                        {{ $proposicao->data_protocolo?->format('d/m/Y \à\s H:i') ?? 'Não informada' }}
                                    </time>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                                    Protocolada por
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->protocoladoPor?->name ?? 'Usuário indisponível' }}
                                </dd>
                            </div>
                        </dl>
                    </section>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
