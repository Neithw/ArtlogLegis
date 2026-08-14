@php
    $vereador = $vereador ?? null;
    $edicao = $vereador !== null;
    $usuarioIsRoot = $usuarioIsRoot ?? false;
@endphp

<div class="grid gap-6 p-4 sm:p-6 md:grid-cols-2">
    @if (!$edicao && $usuarioIsRoot)
        <x-ui::select name="camara_id" label="Câmara" x-model="camaraId" x-on:change="alterarCamara" required>
            <option value="">Selecione uma Câmara</option>

            @foreach ($camaras as $camara)
                <option value="{{ $camara->id }}">
                    {{ $camara->nome }}
                </option>
            @endforeach
        </x-ui::select>
    @else
        @php
            $nomeCamara = $edicao ? $vereador->camara->nome : $camaras->first()?->nome ?? 'Câmara não encontrada';
        @endphp

        <div class="md:col-span-2">
            <p class="text-sm font-medium text-slate-700 dark:text-neutral-300">
                Câmara
            </p>

            <div
                class="mt-1 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3
                       dark:border-neutral-800 dark:bg-neutral-950">
                <p class="text-sm font-semibold text-slate-950 dark:text-neutral-100">
                    {{ $nomeCamara }}
                </p>

                <p class="mt-1 text-xs text-slate-500 dark:text-neutral-500">
                    @if ($edicao)
                        A Câmara vinculada não pode ser alterada após o cadastro.
                    @else
                        O vereador será vinculado automaticamente à sua Câmara.
                    @endif
                </p>
            </div>

            @error('camara_id')
                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                    {{ $message }}
                </p>
            @enderror
        </div>
    @endif

    <div @class(['md:col-span-2' => !$edicao || !$usuarioIsRoot])>
        @if ($edicao)
            <x-ui::select name="user_id" label="Conta de acesso">
                <option value="">Sem conta vinculada</option>

                @foreach ($usuariosDisponiveis as $usuario)
                    <option value="{{ $usuario->id }}" @selected((string) old('user_id', $vereador->user_id) === (string) $usuario->id)>
                        {{ $usuario->name }} — {{ $usuario->email }}

                        @if ($usuario->trashed())
                            (conta arquivada)
                        @elseif (!$usuario->ativo)
                            (conta desativada)
                        @endif
                    </option>
                @endforeach
            </x-ui::select>
        @else
            <x-ui::select name="user_id" label="Conta de acesso" x-model="userId" x-bind:disabled="!camaraId">
                <option value="">Sem conta vinculada</option>

                <template x-for="usuario in usuariosFiltrados" x-bind:key="usuario.id">
                    <option x-bind:value="String(usuario.id)" x-text="`${usuario.name} — ${usuario.email}`"></option>
                </template>
            </x-ui::select>

            <p x-show="!camaraId" x-cloak class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
                Selecione uma Câmara para visualizar as contas disponíveis.
            </p>

            <p x-show="camaraId && usuariosFiltrados.length === 0" x-cloak
                class="mt-1.5 text-xs text-slate-500 dark:text-neutral-500">
                Nenhuma conta está disponível para esta Câmara.
            </p>
        @endif
    </div>

    <x-ui::input name="nome" label="Nome completo" :value="$vereador?->nome" required autofocus />

    <x-ui::input name="nome_parlamentar" label="Nome parlamentar" :value="$vereador?->nome_parlamentar" />

    <x-ui::input name="email_institucional" label="E-mail institucional" type="email" :value="$vereador?->email_institucional" />

    <x-ui::input name="telefone_institucional" label="Telefone institucional" type="text" :value="$vereador?->telefone_institucional" />

    <div class="md:col-span-2">
        <x-ui::textarea name="biografia" label="Biografia institucional" :value="$vereador?->biografia" rows="6" />
    </div>
</div>
