<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                Administração
            </p>

            <h2 class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-neutral-100">
                Cadastrar Câmara
            </h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-ui::card>
                <header class="border-b border-slate-200 px-4 py-5 sm:px-6 dark:border-neutral-800">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 dark:bg-neutral-800 dark:text-neutral-400">
                            <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-neutral-100">
                                Dados institucionais
                            </h3>

                            <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                                Informe os dados necessários para cadastrar uma nova Câmara.
                            </p>
                        </div>
                    </div>
                </header>

                <form action="{{ route('camaras.store') }}" method="POST">
                    @csrf

                    <div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
                        <div>
                            <label for="nome"
                                class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                Nome da Câmara
                                <span class="text-red-600 dark:text-red-400" aria-hidden="true">*</span>
                            </label>

                            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                                autofocus autocomplete="organization" placeholder="Ex.: Câmara Municipal de Uberlândia"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600">

                            @error('nome')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label for="cnpj"
                                    class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                                    CNPJ
                                </label>

                                <span class="text-xs text-slate-400 dark:text-neutral-500">
                                    Opcional
                                </span>
                            </div>

                            <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}"
                                inputmode="numeric" maxlength="18" x-mask="99.999.999/9999-99"
                                placeholder="00.000.000/0000-00"
                                class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-slate-950 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-600">

                            <p class="mt-2 text-xs text-slate-500 dark:text-neutral-500">
                                Informe o CNPJ da instituição, quando disponível.
                            </p>

                            @error('cnpj')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <footer
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-6 dark:border-neutral-800 dark:bg-neutral-950/50">
                        <x-ui::button :href="route('camaras.index')" variant="secondary">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            Cancelar
                        </x-ui::button>

                        <x-ui::button type="submit">
                            <i class="fa-solid fa-plus" aria-hidden="true"></i>
                            Cadastrar Câmara
                        </x-ui::button>
                    </footer>
                </form>
            </x-ui::card>
        </div>
    </div>
</x-app-layout>
