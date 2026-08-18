@php
    $user = Auth::user();

    $grupos = [
        [
            'nome' => 'Administração',
            'icone' => 'fa-sliders',
            'rotas' => ['camaras.*', 'usuarios.*'],
            'itens' => array_values(
                array_filter([
                    $user->can('viewAny', \App\Models\Camara::class)
                        ? [
                            'nome' => $user->isRoot() ? 'Câmaras' : 'Dados institucionais',
                            'icone' => 'fa-building-columns',
                            'rota' => 'camaras.index',
                            'rotas' => ['camaras.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\User::class)
                        ? [
                            'nome' => 'Usuários',
                            'icone' => 'fa-users',
                            'rota' => 'usuarios.index',
                            'rotas' => ['usuarios.*'],
                        ]
                        : null,
                ]),
            ),
        ],
        [
            'nome' => 'Estrutura parlamentar',
            'icone' => 'fa-landmark',
            'rotas' => ['legislaturas.*', 'vereadores.*', 'partidos.*', 'mandatos.*'],
            'itens' => array_values(
                array_filter([
                    $user->can('viewAny', \App\Models\Partido::class)
                        ? [
                            'nome' => 'Partidos',
                            'icone' => 'fa-flag',
                            'rota' => 'partidos.index',
                            'rotas' => ['partidos.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\Legislatura::class)
                        ? [
                            'nome' => 'Legislaturas',
                            'icone' => 'fa-calendar-days',
                            'rota' => 'legislaturas.index',
                            'rotas' => ['legislaturas.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\Vereador::class)
                        ? [
                            'nome' => 'Vereadores',
                            'icone' => 'fa-user-tie',
                            'rota' => 'vereadores.index',
                            'rotas' => ['vereadores.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\Mandato::class)
                        ? [
                            'nome' => 'Mandatos',
                            'icone' => 'fa-id-card',
                            'rota' => 'mandatos.index',
                            'rotas' => ['mandatos.*'],
                        ]
                        : null,
                ]),
            ),
        ],
        [
            'nome' => 'Processo legislativo',
            'icone' => 'fa-file-lines',
            'rotas' => ['tipos-proposicao.*', 'proposicoes.*', 'tramitacoes.*', 'unidades-tramitacao.*', 'sessoes.*'],
            'itens' => array_values(
                array_filter([
                    $user->can('viewAny', \App\Models\UnidadeTramitacao::class)
                        ? [
                            'nome' => 'Unidades de tramitação',
                            'icone' => 'fa-sitemap',
                            'rota' => 'unidades-tramitacao.index',
                            'rotas' => ['unidades-tramitacao.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\TipoProposicao::class)
                        ? [
                            'nome' => 'Tipos de proposição',
                            'icone' => 'fa-tags',
                            'rota' => 'tipos-proposicao.index',
                            'rotas' => ['tipos-proposicao.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\Proposicao::class)
                        ? [
                            'nome' => 'Proposições',
                            'icone' => 'fa-file-signature',
                            'rota' => 'proposicoes.index',
                            'rotas' => ['proposicoes.*', 'tramitacoes.*'],
                        ]
                        : null,

                    $user->can('viewAny', \App\Models\Sessao::class)
                        ? [
                            'nome' => 'Sessões',
                            'icone' => 'fa-scale-balanced',
                            'rota' => 'sessoes.index',
                            'rotas' => ['sessoes.*'],
                        ]
                        : null,
                ]),
            ),
        ],
    ];
@endphp

<div x-data>
    {{-- Topbar --}}
    <header
        class="fixed inset-x-0 top-0 z-50 flex h-16 items-center justify-between
               border-b border-slate-200 bg-white px-4
               text-slate-700 transition-colors
               dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-200">
        <div class="flex items-center gap-3">
            {{-- Hambúrguer somente no celular --}}
            <button type="button" @click="$store.layout.sidebarOpen = !$store.layout.sidebarOpen"
                class="inline-flex size-10 items-center justify-center rounded-lg
                       text-slate-500 transition hover:bg-slate-100 hover:text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       dark:text-neutral-400 dark:hover:bg-slate-800 dark:hover:text-white
                       lg:hidden"
                aria-label="Abrir menu">
                <i class="fa-solid fa-bars fa-fw text-lg"></i>
            </button>

            <a href="{{ route('dashboard') }}" wire:navigate.hover class="flex items-center">
                <img src="{{ asset('images/artlog.svg') }}" alt="{{ config('app.name', 'ArtLog Legis') }}"
                    class="block h-10 w-auto object-contain">
            </a>
        </div>

        {{-- Câmara centralizada --}}
        <div
            class="app-context pointer-events-none absolute inset-y-0 left-0 right-0 hidden
           items-center justify-center md:flex">
            <div
                class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-2
                       text-sm font-medium text-slate-600
                       dark:bg-neutral-900 dark:text-neutral-300">
                <i @class([
                    'fa-solid fa-fw text-slate-400 dark:text-neutral-500',
                    'fa-earth-americas' => $user->isRoot(),
                    'fa-building-columns' => !$user->isRoot(),
                ])></i>

                <span class="max-w-[32vw] truncate">
                    {{ $user->camara?->nome ?? 'Administração global' }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Tema --}}
            <button type="button" @click="$store.layout.toggleTheme()"
                :title="$store.layout.darkMode ? 'Usar tema claro' : 'Usar tema escuro'"
                :aria-label="$store.layout.darkMode ? 'Usar tema claro' : 'Usar tema escuro'"
                class="inline-flex size-10 items-center justify-center rounded-lg
                       text-slate-500 transition hover:bg-slate-100 hover:text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       dark:text-neutral-400 dark:hover:bg-slate-800 dark:hover:text-white">
                <i class="fa-solid fa-fw text-base" :class="$store.layout.darkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>

            {{-- Conta --}}
            <div x-data="{ profileOpen: false }" @click.outside="profileOpen = false"
                @keydown.escape.window="profileOpen = false" class="relative">
                <button type="button" @click="profileOpen = !profileOpen" :aria-expanded="profileOpen"
                    class="flex items-center gap-3 rounded-lg px-2 py-1.5
                           transition hover:bg-slate-100
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           dark:hover:bg-slate-800">
                    <span
                        class="inline-flex size-9 items-center justify-center rounded-full
                               bg-indigo-100 text-sm font-semibold text-indigo-700
                               dark:bg-indigo-500/20 dark:text-indigo-300">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>

                    <span class="hidden text-left lg:block">
                        <span
                            class="block max-w-40 truncate text-sm font-medium
                                   text-slate-700 dark:text-neutral-200">
                            {{ $user->name }}
                        </span>
                    </span>

                    <i class="fa-solid fa-chevron-down fa-fw hidden text-xs
                               text-slate-400 transition-transform sm:block"
                        :class="profileOpen ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="profileOpen" x-transition.origin.top.right style="display: none"
                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl
                           border border-slate-200 bg-white shadow-lg
                           dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="px-4 py-3">
                        <p
                            class="truncate text-sm font-medium
                                   text-slate-900 dark:text-neutral-100">
                            {{ $user->name }}
                        </p>

                        <p class="truncate text-xs text-slate-500 dark:text-neutral-400">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div class="border-t border-slate-200 dark:border-neutral-700"></div>

                    <a href="{{ route('profile.show') }}" wire:navigate.hover @click="profileOpen = false"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm
                               text-slate-600 transition hover:bg-slate-50 hover:text-slate-900
                               dark:text-neutral-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="fa-solid fa-user fa-fw"></i>
                        Perfil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                            class="flex w-full items-center gap-3 px-4 py-2.5 text-left
                                   text-sm text-slate-600 transition
                                   hover:bg-slate-50 hover:text-slate-900
                                   dark:text-neutral-300 dark:hover:bg-slate-800
                                   dark:hover:text-white">
                            <i class="fa-solid fa-arrow-right-from-bracket fa-fw"></i>
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Fundo mobile --}}
    <div x-show="$store.layout.sidebarOpen" x-transition.opacity @click="$store.layout.sidebarOpen = false"
        style="display: none" class="fixed inset-0 top-16 z-30 bg-slate-950/50 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="$store.layout.sidebarOpen ? '!translate-x-0' : '-translate-x-full'"
        class="app-sidebar fixed bottom-0 left-0 top-16 z-40 flex w-64
           -translate-x-full flex-col overflow-hidden
           border-r border-slate-200 bg-white shadow-xl
           dark:border-neutral-800 dark:bg-neutral-900
           lg:translate-x-0 lg:shadow-none">
        {{-- Cabeçalho da sidebar --}}
        <div class="flex h-20 shrink-0 items-center justify-between
                   border-b border-slate-200 px-4
                   dark:border-neutral-800"
            :class="$store.layout.sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
            <div data-sidebar-label class="min-w-0" :class="$store.layout.sidebarLabelsVisible ? '' : 'lg:hidden'">
                <p class="font-semibold text-slate-800 dark:text-neutral-100">
                    Menu
                </p>

                <p class="text-xs text-slate-500 dark:text-neutral-400">
                    Navegação principal
                </p>
            </div>

            <button type="button"
                @click="
                    if (window.innerWidth >= 1024) {
                        $store.layout.toggleSidebar();
                    } else {
                        $store.layout.sidebarOpen = false;
                    }
                "
                class="inline-flex size-9 shrink-0 items-center justify-center
                       rounded-lg bg-slate-100 text-slate-500 transition
                       hover:bg-slate-200 hover:text-slate-900
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       dark:bg-neutral-800 dark:text-neutral-400
                       dark:hover:bg-slate-700 dark:hover:text-white"
                :title="$store.layout.sidebarCollapsed ? 'Expandir menu' : 'Recolher menu'">
                <span class="inline-flex lg:hidden">
                    <i class="fa-solid fa-xmark fa-fw"></i>
                </span>

                <span data-sidebar-expand-icon class="hidden lg:inline-flex">
                    <i class="fa-solid fa-angles-right fa-fw"></i>
                </span>

                <span data-sidebar-collapse-icon class="hidden lg:inline-flex">
                    <i class="fa-solid fa-angles-left fa-fw"></i>
                </span>
            </button>
        </div>

        <nav class="flex-1 space-y-2 overflow-y-auto p-3">
            {{-- Visão geral --}}
            <a href="{{ route('dashboard') }}" wire:navigate.hover
                wire:current.exact="!bg-indigo-50 !text-indigo-700 dark:!bg-neutral-800 dark:!text-white"
                @click="$store.layout.sidebarOpen = false" title="Visão geral"
                :class="$store.layout.sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                class="flex items-center gap-3 rounded-lg px-3 py-2.5
           text-sm font-medium text-slate-600 transition
           hover:bg-slate-100 hover:text-slate-950
           dark:text-neutral-400 dark:hover:bg-neutral-800
           dark:hover:text-white">
                <i class="fa-solid fa-table-cells-large fa-fw shrink-0 text-base"></i>

                <span data-sidebar-label class="whitespace-nowrap"
                    :class="$store.layout.sidebarLabelsVisible ? '' : 'lg:hidden'">
                    Visão geral
                </span>
            </a>

            {{-- Grupos --}}
            @foreach ($grupos as $grupo)
                @if (count($grupo['itens']) > 0)
                    <div x-data="{
                        open: {{ request()->routeIs(...$grupo['rotas']) ? 'true' : 'false' }}
                    }">
                        <button type="button"
                            @click="
                                if ($store.layout.sidebarCollapsed && window.innerWidth >= 1024) {
                                    $store.layout.toggleSidebar();
                                    open = true;
                                } else {
                                    open = !open;
                                }
                            "
                            :aria-expanded="open"
                            :class="$store.layout.sidebarCollapsed ? 'lg:justify-center lg:px-0' : ''"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5
                                   text-sm font-medium text-slate-600 transition
                                   hover:bg-slate-100 hover:text-slate-950
                                   dark:text-neutral-400 dark:hover:bg-slate-800
                                   dark:hover:text-white"
                            title="{{ $grupo['nome'] }}">
                            <i
                                class="fa-solid {{ $grupo['icone'] }}
                                       fa-fw shrink-0 text-base"></i>

                            <span data-sidebar-label class="flex min-w-0 flex-1 items-center justify-between gap-2"
                                :class="$store.layout.sidebarLabelsVisible ? '' : 'lg:hidden'">
                                <span class="truncate">
                                    {{ $grupo['nome'] }}
                                </span>

                                <i class="fa-solid fa-chevron-down fa-fw shrink-0
                                           text-xs transition-transform"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </span>
                        </button>

                        <div x-show="open" data-sidebar-label
                            @if (!request()->routeIs(...$grupo['rotas'])) style="display: none" @endif
                            :class="$store.layout.sidebarLabelsVisible ? '' : 'lg:hidden'" class="mt-1 space-y-1 ps-4">
                            @foreach ($grupo['itens'] as $item)
                                <a href="{{ route($item['rota']) }}" wire:navigate.hover
                                    wire:current="!bg-indigo-50 !text-indigo-700 font-medium dark:!bg-neutral-800 dark:!text-white"
                                    @click="$store.layout.sidebarOpen = false"
                                    class="flex items-center gap-3 rounded-lg px-3 py-2
           text-sm text-slate-600 transition
           hover:bg-slate-100 hover:text-slate-950
           dark:text-neutral-400 dark:hover:bg-neutral-800
           dark:hover:text-white">
                                    <i
                                        class="fa-solid {{ $item['icone'] }}
                                               fa-fw shrink-0 text-sm"></i>

                                    <span>{{ $item['nome'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
    </aside>
</div>
