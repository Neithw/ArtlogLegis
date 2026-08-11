<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-indigo-600">
                Administração legislativa
            </p>

            <h2 class="text-2xl font-semibold leading-tight text-gray-900">
                Nova proposição
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('proposicoes.store') }}" class="space-y-6">
                @csrf

                <div class="overflow-hidden rounded-xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Dados da proposição
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Informe os dados que compõem a proposição.
                        </p>
                    </div>

                    <div class="grid gap-6 p-6 md:grid-cols-2" x-data="{
                        camaraId: @js((string) old('camara_id', $usuarioIsRoot ? '' : $camaras->first()?->id)),
                        legislaturaId: @js((string) old('legislatura_id', '')),
                        tipoProposicaoId: @js((string) old('tipo_proposicao_id', '')),
                        legislaturas: @js($legislaturas),
                        tiposProposicao: @js($tiposProposicao),
                        autorMandatoId: @js((string) old('autor_mandato_id', '')),
                    
                        mandatos: @js(
    $mandatos
        ->map(
            fn($mandato) => [
                'id' => $mandato->id,
                'camara_id' => $mandato->vereador->camara_id,
                'legislatura_id' => $mandato->legislatura_id,
                'nome' => $mandato->vereador->nome_parlamentar ?: $mandato->vereador->nome,
            ],
        )
        ->values(),
),
                    
                        get mandatosFiltrados() {
                            return this.mandatos.filter(mandato =>
                                String(mandato.camara_id) === String(this.camaraId) &&
                                String(mandato.legislatura_id) === String(this.legislaturaId)
                            )
                        },
                    
                        legislaturasFiltradas() {
                            return this.legislaturas.filter(
                                legislatura =>
                                String(legislatura.camara_id) === String(this.camaraId)
                            );
                        },
                    
                        tiposProposicaoFiltrados() {
                            return this.tiposProposicao.filter(
                                tipo =>
                                String(tipo.camara_id) === String(this.camaraId)
                            );
                        }
                    }">
                        @if ($usuarioIsRoot)

                            <div class="md:col-span-2">
                                <x-label for="camara_id" value="Câmara" />

                                <select id="camara_id" name="camara_id" x-model="camaraId"
                                    @change="
                                    legislaturaId = '';
                                    tipoProposicaoId = '';
                                    autorMandatoId = ''
                                    "
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="">
                                        Selecione uma Câmara
                                    </option>

                                    @foreach ($camaras as $camara)
                                        <option value="{{ $camara->id }}">
                                            {{ $camara->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                <x-input-error for="camara_id" class="mt-2" />
                            </div>
                        @else
                            <div class="md:col-span-2">
                                <x-label value="Câmara" />

                                <div class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $camaras->first()?->nome ?? 'Câmara não encontrada' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        A proposição será vinculada automaticamente à sua Câmara.
                                    </p>
                                </div>

                                <x-input-error for="camara_id" class="mt-2" />
                            </div>
                        @endif
                        <div>
                            <x-label for="legislatura_id" value="Legislatura" />

                            <select id="legislatura_id" name="legislatura_id" x-model="legislaturaId"
                                @change="autorMandatoId = ''" :disabled="!camaraId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500"
                                required>
                                <option value="">
                                    Selecione uma Legislatura
                                </option>

                                <template x-for="legislatura in legislaturasFiltradas()" :key="legislatura.id">
                                    <option :value="legislatura.id"
                                        :selected="String(legislatura.id) === String(legislaturaId)"
                                        x-text="`${legislatura.numero}ª Legislatura`">
                                    </option>
                                </template>
                            </select>

                            <x-input-error for="legislatura_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="autor_mandato_id" value="Autor principal" />

                            <select id="autor_mandato_id" name="autor_mandato_id" x-model="autorMandatoId"
                                :disabled="!legislaturaId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500"
                                required>
                                <option value="">
                                    Selecione um autor
                                </option>

                                <template x-for="mandato in mandatosFiltrados" :key="mandato.id">
                                    <option :value="mandato.id"
                                        :selected="String(mandato.id) === String(autorMandatoId)" x-text="mandato.nome">
                                    </option>
                                </template>
                            </select>

                            <p x-show="legislaturaId && mandatosFiltrados.length === 0"
                                class="mt-2 text-sm text-gray-500">
                                Nenhum mandato disponível para esta legislatura.
                            </p>

                            <x-input-error for="autor_mandato_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="tipo_proposicao_id" value="Tipo de Proposição" />

                            <select id="tipo_proposicao_id" name="tipo_proposicao_id" x-model="tipoProposicaoId"
                                :disabled="!camaraId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500"
                                required>
                                <option value="">
                                    Selecione um Tipo
                                </option>

                                <template x-for="tipo in tiposProposicaoFiltrados()" :key="tipo.id">
                                    <option :value="tipo.id"
                                        :selected="String(tipo.id) === String(tipoProposicaoId)" x-text="tipo.nome">
                                    </option>
                                </template>
                            </select>

                            <x-input-error for="tipo_proposicao_id" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="ementa" value="Ementa" />

                            <x-input id="ementa" name="ementa" type="text" class="mt-1 block w-full"
                                :value="old('ementa')" />

                            <x-input-error for="ementa" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="assunto" value="Assunto" />

                            <x-input id="assunto" name="assunto" type="text" class="mt-1 block w-full"
                                :value="old('assunto')" />

                            <x-input-error for="assunto" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="texto_integral" value="Texto Integral" />

                            <textarea id="texto_integral" name="texto_integral" rows="12"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('texto_integral') }}</textarea>

                            <x-input-error for="texto_integral" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-label for="area_tematica" value="Área Temática" />

                            <x-input id="area_tematica" name="area_tematica" type="text" class="mt-1 block w-full"
                                :value="old('area_tematica')" />

                            <x-input-error for="area_tematica" class="mt-2" />
                        </div>

                        <div class="md:col-span-2" x-data="{
                            proximoId: 1,
                            novaPalavra: '',
                        
                            palavrasChave: @js(collect(old('palavras_chave', []))->filter(fn($valor) => filled($valor))->values()->map(fn($valor, $index) => ['id' => $index + 1, 'valor' => $valor])),
                        
                            adicionarPalavraChave() {
                                const valor = this.novaPalavra.trim();
                        
                                if (!valor) {
                                    return;
                                }
                        
                                const repetida = this.palavrasChave.some(
                                    palavra =>
                                    palavra.valor.toLowerCase() === valor.toLowerCase()
                                );
                        
                                if (repetida) {
                                    this.novaPalavra = '';
                                    return;
                                }
                        
                                this.palavrasChave.push({
                                    id: this.proximoId++,
                                    valor: valor
                                });
                        
                                this.novaPalavra = '';
                            },
                        
                            removerPalavraChave(id) {
                                this.palavrasChave = this.palavrasChave.filter(
                                    palavra => palavra.id !== id
                                );
                            }
                        }" x-init="proximoId = palavrasChave.length ?
                            Math.max(...palavrasChave.map(palavra => palavra.id)) + 1 :
                            1">
                            <x-label for="nova_palavra_chave" value="Palavras-chave" />

                            <p class="mt-1 text-xs text-gray-500">
                                Digite um termo e pressione Enter para adicioná-lo.
                            </p>

                            <div
                                class="mt-2 rounded-md border border-gray-300 bg-white p-3 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                                <div x-show="palavrasChave.length > 0" class="mb-3 flex flex-wrap gap-2">
                                    <template x-for="palavra in palavrasChave" :key="palavra.id">
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                                            <span x-text="palavra.valor"></span>

                                            <button type="button" @click="removerPalavraChave(palavra.id)"
                                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-indigo-500 transition hover:bg-indigo-200 hover:text-indigo-800"
                                                title="Remover palavra-chave" aria-label="Remover palavra-chave">
                                                ×
                                            </button>

                                            <input type="hidden" name="palavras_chave[]" :value="palavra.valor">
                                        </span>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input id="nova_palavra_chave" type="text" x-model="novaPalavra"
                                        :name="novaPalavra.trim() ? 'palavras_chave[]' : null"
                                        @keydown.enter.prevent="adicionarPalavraChave" maxlength="100"
                                        placeholder="Ex.: saúde pública"
                                        class="min-w-0 flex-1 border-0 p-0 text-sm shadow-none placeholder:text-gray-400 focus:border-0 focus:ring-0">

                                    <button type="button" @click="adicionarPalavraChave"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-lg font-semibold text-white transition hover:bg-indigo-700"
                                        title="Adicionar palavra-chave" aria-label="Adicionar palavra-chave">
                                        +
                                    </button>
                                </div>
                            </div>

                            <x-input-error for="palavras_chave" class="mt-2" />
                            <x-input-error for="palavras_chave.*" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('proposicoes.index') }}"
                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50">
                        Cancelar
                    </a>

                    <x-button>
                        Cadastrar proposição
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
