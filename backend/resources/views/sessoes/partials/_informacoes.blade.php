<section
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                       dark:border-neutral-800 dark:bg-neutral-900">
    <header class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
        <h3
            class="flex items-center gap-2 text-base font-semibold
                               text-slate-950 dark:text-neutral-100">
            <i class="fa-solid fa-circle-info text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>
            Informações da sessão
        </h3>

        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
            Dados institucionais e informações de registro.
        </p>
    </header>

    <dl class="grid gap-x-8 gap-y-6 p-6 sm:grid-cols-2">
        @if ($usuarioIsRoot)
            <div>
                <dt
                    class="text-xs font-semibold uppercase tracking-wide
                                       text-slate-500 dark:text-neutral-500">
                    Câmara
                </dt>

                <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                    {{ $sessao->camara->nome }}
                </dd>
            </div>
        @endif

        <div>
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                Tipo da sessão
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $tipo }}
            </dd>
        </div>

        <div>
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                Período da legislatura
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->legislatura->data_inicio->format('d/m/Y') }}
                a
                {{ $sessao->legislatura->data_fim->format('d/m/Y') }}
            </dd>
        </div>

        <div>
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                Cadastrada por
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->criadoPor->name }}
            </dd>
        </div>

        <div>
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                Data do cadastro
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->created_at->format('d/m/Y \à\s H:i') }}
            </dd>
        </div>

        <div>
            <dt
                class="text-xs font-semibold uppercase tracking-wide
                                   text-slate-500 dark:text-neutral-500">
                Última atualização
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->updated_at->format('d/m/Y \à\s H:i') }}
            </dd>
        </div>
    </dl>
</section>
