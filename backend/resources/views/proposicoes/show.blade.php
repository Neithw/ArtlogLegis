<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold text-gray-900">
                Detalhes da proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700"
                    role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
                    role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow">
                <div
                    class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Rascunho #{{ $proposicao->id }}
                    </h3>
                    <div class="flex items-center gap-2">
                        @can('update', $proposicao)
                            <a href="{{ route('proposicoes.edit', $proposicao) }}"
                                class="inline-flex items-center justify-center rounded-lg bg-yellow-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-yellow-700">
                                Editar Proposição
                            </a>
                        @endcan

                        <a href="{{ route('proposicoes.index') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                            Voltar
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <section class="p-6">
                        <h4 class="text-base font-semibold text-gray-900">
                            Identificação
                        </h4>

                        <dl class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Câmara
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->camara->nome }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Legislatura
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->legislatura->numero }}ª Legislatura
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Autor principal
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->autorMandato->vereador->nome_parlamentar ?: $proposicao->autorMandato->vereador->nome }}
                                </dd>

                                @if ($proposicao->autorMandato->trashed())
                                    <dd class="mt-1 text-xs text-gray-500">
                                        Mandato arquivado
                                    </dd>
                                @endif
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Tipo de proposição
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->tipoProposicao->nome }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Criado por
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->criadoPor->name }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Situação
                                </dt>
                                <dd class="mt-2">
                                    <span
                                        class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800">
                                        {{ ucfirst(str_replace('_', ' ', $proposicao->situacao)) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="p-6">
                        <h4 class="text-base font-semibold text-gray-900">
                            Informações gerais
                        </h4>

                        <dl class="mt-5 space-y-5">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Ementa
                                </dt>

                                <dd class="mt-1 text-sm leading-6 text-gray-900">
                                    {{ $proposicao->ementa ?: 'Não informada' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Assunto
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->assunto ?: 'Não informado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Área temática
                                </dt>

                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $proposicao->area_tematica ?: 'Não informada' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">
                                    Palavras-chave
                                </dt>

                                <dd class="mt-2 flex flex-wrap gap-2">
                                    @forelse ($proposicao->palavras_chave ?? [] as $palavraChave)
                                        <span
                                            class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                                            {{ $palavraChave }}
                                        </span>
                                    @empty
                                        <span class="text-sm text-gray-500">
                                            Nenhuma palavra-chave informada.
                                        </span>
                                    @endforelse
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="p-6">
                        <h4 class="text-base font-semibold text-gray-900">
                            Texto integral
                        </h4>

                        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            {{-- blade-formatter-disable-next-line --}}
                            <p class="whitespace-pre-line text-sm leading-7 text-gray-700">{{ $proposicao->texto_integral ?: 'Não informado' }}</p>
                        </div>
                    </section>

                    <section class="bg-gray-50 px-6 py-4">
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Cadastrada em
                                </dt>

                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ $proposicao->created_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Última atualização
                                </dt>

                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ $proposicao->updated_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                        </dl>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
