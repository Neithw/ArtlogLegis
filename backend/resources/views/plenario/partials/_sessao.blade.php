@php
    $tipo = \App\Models\Sessao::TIPOS[$sessao->tipo] ?? ucfirst(str_replace('_', ' ', $sessao->tipo));

    $situacao = \App\Models\Sessao::SITUACOES[$sessao->situacao] ?? ucfirst(str_replace('_', ' ', $sessao->situacao));

    $titulo = $sessao->numero ? "{$sessao->numero}ª Sessão {$tipo}" : "Sessão {$tipo}";

    $nomeParlamentar = $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome;

    $classesSituacao = match ($sessao->situacao) {
        'convocada' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300',
        'aberta' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
        'suspensa' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300',
        'encerrada' => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300',
    };

    $iconeSituacao = match ($sessao->situacao) {
        'convocada' => 'fa-bullhorn',
        'aberta' => 'fa-door-open',
        'suspensa' => 'fa-pause',
        'encerrada' => 'fa-circle-check',
        default => 'fa-circle',
    };
@endphp

<section
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
           dark:border-neutral-800 dark:bg-neutral-900">
    <div class="p-5 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                           bg-indigo-50 text-indigo-600
                           dark:bg-indigo-950/60 dark:text-indigo-300">
                    <i class="fa-solid fa-landmark" aria-hidden="true"></i>
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-500 dark:text-neutral-400">
                        {{ $sessao->camara->nome }}
                    </p>

                    <h3 class="mt-0.5 text-lg font-semibold text-slate-950 dark:text-neutral-100">
                        {{ $titulo }}
                    </h3>
                </div>
            </div>

            <span
                class="inline-flex self-start items-center gap-2 rounded-full px-3 py-1.5
                       text-xs font-semibold {{ $classesSituacao }}">
                <i class="fa-solid {{ $iconeSituacao }}" aria-hidden="true"></i>
                {{ $situacao }}
            </span>
        </div>

        <div class="mt-5 flex items-center gap-3 rounded-xl bg-slate-50 p-4
                   dark:bg-neutral-800">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                       bg-white text-slate-500 shadow-sm
                       dark:bg-neutral-800 dark:text-neutral-300">
                <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
            </div>

            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                    Parlamentar autenticado
                </p>

                <p class="truncate font-semibold text-slate-950 dark:text-neutral-100">
                    {{ $nomeParlamentar }}
                </p>
            </div>
        </div>
    </div>

    <dl
        class="grid grid-cols-1 divide-y divide-slate-200 border-t border-slate-200
               sm:grid-cols-3 sm:divide-x sm:divide-y-0
               dark:divide-neutral-800 dark:border-neutral-800">
        <div class="px-5 py-4">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                Data e horário
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->data_hora_inicio_previsto->format('d/m/Y \à\s H:i') }}
            </dd>
        </div>

        <div class="px-5 py-4">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                Local
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->local ?: 'Local a definir' }}
            </dd>
        </div>

        <div class="px-5 py-4">
            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-neutral-500">
                Legislatura
            </dt>

            <dd class="mt-1 text-sm font-medium text-slate-950 dark:text-neutral-100">
                {{ $sessao->legislatura->numero }}ª Legislatura
            </dd>
        </div>
    </dl>
</section>
