<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Atividade legislativa
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Editar sessão
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sessoes.update', $sessao) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <x-ui::card>
                    <header
                        class="border-b border-slate-200 px-4 py-5
                               sm:px-6 dark:border-neutral-800">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Dados da sessão
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Atualize os dados da sessão.
                        </p>
                    </header>

                    @include('sessoes._form', [
                        'sessao' => $sessao,
                    ])
                </x-ui::card>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui::button :href="route('sessoes.index')" variant="secondary">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Cancelar
                    </x-ui::button>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        Atualizar sessão
                    </x-ui::button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
