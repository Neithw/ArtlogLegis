@if (in_array($sessao->situacao, ['em_preparacao', 'convocada'], true))
    @can('cancelar', $sessao)
        <section
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm
                               dark:border-neutral-800 dark:bg-neutral-900">
            <header class="border-b border-slate-200 px-6 py-5 dark:border-neutral-800">
                <h3
                    class="flex items-center gap-2 text-base font-semibold
                                       text-slate-950 dark:text-neutral-100">
                    <i class="fa-solid fa-ban text-slate-400 dark:text-neutral-500" aria-hidden="true"></i>
                    Cancelar sessão
                </h3>

                <p class="mt-1 text-sm text-slate-500 dark:text-neutral-400">
                    O cancelamento será definitivo e permanecerá registrado no histórico.
                </p>
            </header>

            <form method="POST" action="{{ route('sessoes.cancelar', $sessao) }}"
                onsubmit="return confirm('Deseja cancelar esta sessão? Esta ação não poderá ser desfeita.');">
                @csrf
                @method('PATCH')

                <div class="space-y-5 p-6">
                    <div>
                        <label for="observacao_cancelamento"
                            class="block text-sm font-medium text-slate-700 dark:text-neutral-300">
                            Justificativa
                        </label>

                        <textarea id="observacao_cancelamento" name="observacao" rows="4" maxlength="2000"
                            placeholder="Informe o motivo do cancelamento." required
                            class="mt-1 block w-full rounded-lg border-slate-300 bg-white
                                               text-slate-950 shadow-sm placeholder:text-slate-400
                                               focus:border-indigo-500 focus:ring-indigo-500
                                               dark:border-neutral-800 dark:bg-neutral-950
                                               dark:text-neutral-100 dark:placeholder:text-neutral-600">{{ old('observacao') }}</textarea>

                        <x-input-error for="observacao" class="mt-2 dark:text-red-400" />
                    </div>

                    <div class="flex justify-end">
                        <x-ui::button type="submit" variant="danger">
                            <i class="fa-solid fa-ban" aria-hidden="true"></i>
                            Cancelar sessão
                        </x-ui::button>
                    </div>
                </div>
            </form>
        </section>
    @endcan
@endif
