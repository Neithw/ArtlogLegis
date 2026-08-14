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

    @php
        [$situacao, $variante] = match ($proposicao->situacao) {
            'rascunho' => ['Rascunho', 'warning'],
            'protocolada' => ['Protocolada', 'success'],
            default => [ucfirst(str_replace('_', ' ', $proposicao->situacao)), 'info'],
        };

        $autor = $proposicao->autorMandato?->vereador;
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

            <x-ui::card>
                <header
                    class="flex flex-col gap-4 border-b border-slate-200 px-4 py-5
                           sm:flex-row sm:items-center sm:justify-between sm:px-6
                           dark:border-neutral-800">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                @if ($proposicao->numero !== null && $proposicao->ano !== null)
                                    {{ $proposicao->tipoProposicao?->nome ?? 'Proposição' }}
                                    nº {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                @else
                                    Rascunho #{{ $proposicao->id }}
                                @endif
                            </h3>

                            <x-ui::badge :variant="$variante">
                                {{ $situacao }}
                            </x-ui::badge>
                        </div>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            {{ $proposicao->tipoProposicao?->nome ?? 'Tipo indisponível' }}
                            <span aria-hidden="true">·</span>
                            {{ $proposicao->legislatura?->numero }}ª Legislatura
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <x-ui::button :href="route('proposicoes.index')" variant="secondary">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Voltar
                        </x-ui::button>

                        @if ($proposicao->situacao === 'rascunho')
                            @can('update', $proposicao)
                                <x-ui::button :href="route('proposicoes.edit', $proposicao)" variant="secondary">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                    Editar
                                </x-ui::button>
                            @endcan

                            @can('protocolar', $proposicao)
                                <form action="{{ route('proposicoes.protocolar', $proposicao) }}" method="POST"
                                    onsubmit="return confirm('Deseja realmente protocolar esta proposição? Esta ação não poderá ser desfeita.')">
                                    @csrf
                                    @method('PATCH')

                                    <x-ui::button type="submit">
                                        <i class="fa-solid fa-stamp" aria-hidden="true"></i>
                                        Protocolar
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
                </header>

                <section class="p-4 sm:p-6">
                    <h4 class="text-base font-semibold text-slate-950 dark:text-neutral-100">
                        Identificação
                    </h4>

                    <dl class="mt-5 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Câmara
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->camara?->nome ?? 'Câmara indisponível' }}
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Legislatura
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->legislatura?->numero }}ª Legislatura
                            </dd>
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Autor principal
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $autor?->nome_parlamentar ?: $autor?->nome ?? 'Autor indisponível' }}
                            </dd>

                            @if ($proposicao->autorMandato?->trashed())
                                <dd class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                                    Mandato arquivado
                                </dd>
                            @endif
                        </div>

                        <div>
                            <dt
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Criada por
                            </dt>
                            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->criadoPor?->name ?? 'Usuário indisponível' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                @if ($proposicao->situacao === 'protocolada')
                    <section class="border-t border-slate-200 p-4 sm:p-6 dark:border-neutral-800">
                        <h4 class="text-base font-semibold text-slate-950 dark:text-neutral-100">
                            Protocolo
                        </h4>

                        <dl class="mt-5 grid gap-x-8 gap-y-6 sm:grid-cols-3">
                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                    Número
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->numero }}/{{ $proposicao->ano }}
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                    Protocolada em
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                    <time datetime="{{ $proposicao->data_protocolo?->toIso8601String() }}">
                                        {{ $proposicao->data_protocolo?->format('d/m/Y H:i') ?? 'Não informada' }}
                                    </time>
                                </dd>
                            </div>

                            <div>
                                <dt
                                    class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                    Protocolada por
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                                    {{ $proposicao->protocoladoPor?->name ?? 'Usuário indisponível' }}
                                </dd>
                            </div>
                        </dl>
                    </section>
                @endif
            </x-ui::card>

            <x-ui::card>
                <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                        Conteúdo da proposição
                    </h3>

                    <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                        Texto, classificação temática e palavras-chave.
                    </p>
                </header>

                <div class="space-y-6 p-4 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Ementa
                        </p>
                        <p class="mt-2 text-sm leading-6 text-slate-950 dark:text-neutral-100">
                            {{ $proposicao->ementa ?: 'Não informada' }}
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Assunto
                            </p>
                            <p class="mt-2 text-sm text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->assunto ?: 'Não informado' }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                                Área temática
                            </p>
                            <p class="mt-2 text-sm text-slate-950 dark:text-neutral-100">
                                {{ $proposicao->area_tematica ?: 'Não informada' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Palavras-chave
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @forelse ($proposicao->palavras_chave ?? [] as $palavraChave)
                                <x-ui::badge>
                                    {{ $palavraChave }}
                                </x-ui::badge>
                            @empty
                                <span class="text-sm text-slate-500 dark:text-neutral-500">
                                    Nenhuma palavra-chave informada.
                                </span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
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

                <footer
                    class="grid gap-4 border-t border-slate-200 bg-slate-50 px-4 py-4
                           sm:grid-cols-2 sm:px-6 dark:border-neutral-800 dark:bg-neutral-950/50">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Cadastrada em
                        </p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-neutral-300">
                            {{ $proposicao->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                            Última atualização
                        </p>
                        <p class="mt-1 text-sm text-slate-700 dark:text-neutral-300">
                            {{ $proposicao->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </footer>
            </x-ui::card>

            @if ($proposicao->situacao === 'rascunho')
                @can('delete', $proposicao)
                    <div class="flex justify-end">
                        <form action="{{ route('proposicoes.destroy', $proposicao) }}" method="POST"
                            onsubmit="return confirm('Deseja realmente arquivar esta proposição?')">
                            @csrf
                            @method('DELETE')

                            <x-ui::button type="submit" variant="danger">
                                <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                                Arquivar proposição
                            </x-ui::button>
                        </form>
                    </div>
                @endcan
            @endif
        </div>
    </div>
</x-app-layout>
