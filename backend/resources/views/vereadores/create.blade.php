<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Estrutura parlamentar
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Novo vereador
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @php
                $configuracaoFormulario = [
                    'camaraId' => (string) old('camara_id', $usuarioIsRoot ? '' : $camaras->first()?->id ?? ''),
                    'userId' => (string) old('user_id', ''),
                    'usuarios' => $usuariosDisponiveis->values(),
                ];
            @endphp

            <form action="{{ route('vereadores.store') }}" method="POST" class="space-y-6" x-data="formularioVereador({{ Js::from($configuracaoFormulario) }})">
                @csrf

                <x-ui::card>
                    <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                            Dados do vereador
                        </h3>

                        <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                            Informe os dados públicos e a conta de acesso opcional.
                        </p>
                    </header>

                    @include('vereadores._form', ['vereador' => null])
                </x-ui::card>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-ui::button :href="route('vereadores.index')" variant="secondary">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Cancelar
                    </x-ui::button>

                    <x-ui::button type="submit">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Cadastrar vereador
                    </x-ui::button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
