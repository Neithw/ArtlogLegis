<section
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">
    <div class="flex flex-col gap-5 p-6 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex min-w-0 items-start gap-4">
            <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                                   bg-slate-100 text-slate-500
                                   dark:bg-neutral-800 dark:text-neutral-400">
                <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                        @if ($sessao->numero !== null && $sessao->ano !== null)
                            {{ $sessao->numero }}ª Sessão {{ $tipo }}
                        @else
                            Sessão {{ $tipo }}
                        @endif
                    </h3>

                    <x-ui::badge :variant="$variante">
                        <i class="fa-solid {{ $iconeSituacao }} w-3 shrink-0 text-center leading-none"
                            aria-hidden="true"></i>
                        {{ $situacao }}
                    </x-ui::badge>
                </div>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 dark:text-neutral-300">
                    @if ($sessao->numero !== null && $sessao->ano !== null)
                        Número oficial {{ $sessao->numero }}/{{ $sessao->ano }}.
                    @endif

                    {{ $descricaoSituacao }}
                </p>
            </div>
        </div>

        <div class="flex shrink-0 flex-col items-start gap-2 sm:items-end">
            {{-- Navegação --}}
            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                <x-ui::button :href="route('sessoes.index')" variant="secondary">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Voltar
                </x-ui::button>
            </div>

            {{-- Ações operacionais --}}
            <div class="flex shrink-0 flex-wrap items-center gap-2">
                @if ($sessao->situacao === 'em_preparacao')
                    @can('update', $sessao)
                        <x-ui::button :href="route('sessoes.edit', $sessao)" variant="edit">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            Editar
                        </x-ui::button>
                    @endcan
                @endif

                @if ($sessao->situacao === 'em_preparacao')
                    @can('convocar', $sessao)
                        <form method="POST" action="{{ route('sessoes.convocar', $sessao) }}"
                            onsubmit="return confirm('Deseja convocar esta sessão? A numeração será atribuída e a edição comum será bloqueada.');">
                            @csrf
                            @method('PATCH')

                            <x-ui::button type="submit">
                                <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                                Convocar sessão
                            </x-ui::button>
                        </form>
                    @endcan
                @endif

                @if ($sessao->situacao === 'convocada')
                    @can('abrir', $sessao)
                        <form method="POST" action="{{ route('sessoes.abrir', $sessao) }}"
                            onsubmit="return confirm('Deseja abrir esta sessão?');">
                            @csrf
                            @method('PATCH')

                            <x-ui::button type="submit">
                                <i class="fa-solid fa-door-open" aria-hidden="true"></i>
                                Abrir sessão
                            </x-ui::button>
                        </form>
                    @endcan
                @endif

                @if ($sessao->situacao === 'suspensa')
                    @can('retomar', $sessao)
                        <form method="POST" action="{{ route('sessoes.retomar', $sessao) }}"
                            onsubmit="return confirm('Deseja retomar esta sessão?');">
                            @csrf
                            @method('PATCH')

                            <x-ui::button type="submit">
                                <i class="fa-solid fa-play" aria-hidden="true"></i>
                                Retomar sessão
                            </x-ui::button>
                        </form>
                    @endcan
                @endif

                @if ($sessao->situacao === 'aberta')
                    @can('encerrar', $sessao)
                        <form method="POST" action="{{ route('sessoes.encerrar', $sessao) }}"
                            onsubmit="return confirm('Deseja encerrar esta sessão? Esta ação não poderá ser desfeita.');">
                            @csrf
                            @method('PATCH')

                            <x-ui::button type="submit" variant="danger">
                                <i class="fa-solid fa-circle-stop" aria-hidden="true"></i>
                                Encerrar sessão
                            </x-ui::button>
                        </form>
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
                Data prevista
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->data_hora_inicio_previsto->format('d/m/Y') }}
            </dd>
        </div>

        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                Horário
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->data_hora_inicio_previsto->format('H:i') }}
            </dd>
        </div>

        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                Local
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->local ?: 'Local a definir' }}
            </dd>
        </div>

        <div class="bg-slate-50 px-6 py-4 dark:bg-neutral-950/50">
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-400">
                Legislatura
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->legislatura->numero }}ª Legislatura
            </dd>
        </div>
    </dl>
</section>
