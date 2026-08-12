<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposicaoRequest;
use App\Http\Requests\UpdateProposicaoRequest;
use App\Models\Camara;
use App\Models\Legislatura;
use App\Models\Mandato;
use App\Models\Proposicao;
use App\Models\TipoProposicao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProposicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $proposicoes = Proposicao::query()
            ->with(['camara', 'legislatura', 'tipoProposicao'])
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->latest()
            ->paginate(10);

        return view('proposicoes.index', compact('proposicoes', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camaras = Camara::query()
            ->where('ativo', true)
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->whereKey($usuarioAutenticado->camara_id)
            )
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $legislaturas = Legislatura::query()
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->orderBy('numero')
            ->get(['id', 'numero', 'camara_id']);

        $tiposProposicao = TipoProposicao::query()
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->orderBy('nome')
            ->get(['id', 'nome', 'camara_id']);

        $mandatos = Mandato::query()
            ->with('vereador:id,camara_id,nome,nome_parlamentar')
            ->whereHas(
                'vereador',
                fn($query) => $query
                    ->when(! $usuarioIsRoot, fn($query) => $query
                        ->where('camara_id', $usuarioAutenticado->camara_id))
            )
            ->orderBy('legislatura_id')
            ->get(['id', 'vereador_id', 'legislatura_id']);


        return view('proposicoes.create', compact('camaras', 'legislaturas', 'tiposProposicao', 'mandatos', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProposicaoRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $dadosValidados['criado_por_id'] = $request->user()->id;

        $proposicao = Proposicao::create($dadosValidados);

        return to_route('proposicoes.show', $proposicao)
            ->with('success', 'Proposição cadastrada com sucesso.');
    }

    public function show(Proposicao $proposicao): View
    {
        $proposicao->load([
            'camara',
            'legislatura',
            'tipoProposicao',
            'autorMandato.vereador',
            'criadoPor',
            'protocoladoPor'
        ]);

        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposicao $proposicao): View
    {
        if ($proposicao->situacao !== 'rascunho') {
            abort(403, 'Somente proposições em rascunho podem ser alteradas ou arquivadas.');
        }

        $proposicao->load(['camara']);

        $legislaturas = Legislatura::withTrashed()
            ->where('camara_id', $proposicao->camara_id)
            ->where(
                fn($query) => $query
                    ->whereNull('deleted_at')
                    ->orWhere('id', $proposicao->legislatura_id)
            )
            ->orderBy('numero')
            ->get(['id', 'numero', 'camara_id', 'deleted_at']);

        $tiposProposicao = TipoProposicao::withTrashed()
            ->where('camara_id', $proposicao->camara_id)
            ->where(
                fn($query) => $query
                    ->whereNull('deleted_at')
                    ->orWhere('id', $proposicao->tipo_proposicao_id)
            )
            ->orderBy('nome')
            ->get(['id', 'nome', 'camara_id', 'deleted_at']);

        $mandatos = Mandato::withTrashed()
            ->with('vereador:id,camara_id,nome,nome_parlamentar')
            ->whereHas(
                'vereador',
                fn($query) => $query
                    ->where('camara_id', $proposicao->camara_id)
            )
            ->where(
                fn($query) => $query
                    ->whereNull('deleted_at')
                    ->orWhere('id', $proposicao->autor_mandato_id)
            )
            ->orderBy('legislatura_id')
            ->get(['id', 'vereador_id', 'legislatura_id', 'deleted_at']);

        return view('proposicoes.edit', compact('proposicao', 'legislaturas', 'mandatos', 'tiposProposicao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProposicaoRequest $request, Proposicao $proposicao): RedirectResponse
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($proposicao, $dadosValidados) {
            $proposicao = Proposicao::query()
                ->lockForUpdate()
                ->findOrFail($proposicao->id);

            if ($proposicao->situacao !== 'rascunho') {
                abort(403, 'Somente proposições em rascunho podem ser alteradas.');
            }

            $proposicao->update($dadosValidados);
        });

        return to_route('proposicoes.show', $proposicao)
            ->with('success', 'Proposição atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposicao $proposicao): RedirectResponse
    {
        DB::transaction(function () use ($proposicao) {
            $proposicao = Proposicao::query()
                ->lockForUpdate()
                ->findOrFail($proposicao->id);

            if ($proposicao->situacao !== 'rascunho') {
                abort(403, 'Somente proposições em rascunho podem ser arquivadas.');
            }

            $proposicao->delete();
        });

        return to_route('proposicoes.index')
            ->with('success', 'Proposição arquivada com sucesso.');
    }

    public function arquivadas(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $arquivadas = Proposicao::onlyTrashed()
            ->with(['camara', 'legislatura', 'tipoProposicao', 'criadoPor'])
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->latest('deleted_at')
            ->paginate(10);

        return view('proposicoes.arquivadas', compact('arquivadas', 'usuarioIsRoot'));
    }

    public function restore(Proposicao $proposicao): RedirectResponse
    {
        $proposicao->restore();

        return to_route('proposicoes.arquivadas')
            ->with('success', 'Proposição restaurada com sucesso.');
    }

    public function protocolar(Request $request, Proposicao $proposicao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($proposicao, $usuarioAutenticadoId) {
            $proposicao = Proposicao::query()
                ->lockForUpdate()
                ->findOrFail($proposicao->id);

            if ($proposicao->situacao !== 'rascunho') {
                throw ValidationException::withMessages([
                    'protocolo' => 'Somente proposições em rascunho podem ser protocoladas.'
                ]);
            }

            $camposObrigatorios = [
                'camara_id' => 'Câmara',
                'legislatura_id' => 'Legislatura',
                'tipo_proposicao_id' => 'Tipo de proposição',
                'autor_mandato_id' => 'Autor',
                'ementa' => 'Ementa',
                'assunto' => 'Assunto',
                'texto_integral' => 'Texto integral',
            ];

            $camposAusentes = collect($camposObrigatorios)
                ->filter(
                    fn(string $nome, string $campo) => blank($proposicao->{$campo})
                )
                ->values();

            if ($camposAusentes->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'protocolo' => 'Preencha os campos obrigatórios antes de protocolar: '
                        . $camposAusentes->join(', ', ' e ')
                        . '.'
                ]);
            }

            $tipoProposicao = TipoProposicao::query()
                ->whereKey($proposicao->tipo_proposicao_id)
                ->where('camara_id', $proposicao->camara_id)
                ->lockForUpdate()
                ->first();

            if (! $tipoProposicao) {
                throw ValidationException::withMessages([
                    'protocolo' => 'O tipo da proposição não está disponível para protocolo.'
                ]);
            }

            $camaraValida = Camara::query()
                ->whereKey($proposicao->camara_id)
                ->where('ativo', true)
                ->exists();

            if (! $camaraValida) {
                throw ValidationException::withMessages([
                    'protocolo' => 'A Câmara da proposição não está disponível para protocolo.'
                ]);
            }

            $legislaturaValida = Legislatura::query()
                ->whereKey($proposicao->legislatura_id)
                ->where('camara_id', $proposicao->camara_id)
                ->exists();

            if (! $legislaturaValida) {
                throw ValidationException::withMessages([
                    'protocolo' => 'A legislatura da proposição não está disponível para protocolo.'
                ]);
            }

            $mandatoValido = Mandato::query()
                ->whereKey($proposicao->autor_mandato_id)
                ->where('legislatura_id', $proposicao->legislatura_id)
                ->whereHas(
                    'vereador',
                    fn($query) => $query
                        ->where('camara_id', $proposicao->camara_id)
                )
                ->exists();

            if (! $mandatoValido) {
                throw ValidationException::withMessages([
                    'protocolo' => 'O mandato do autor não está disponível para protocolo.'
                ]);
            }

            $dataProtocolo = now();
            $ano = (int) $dataProtocolo->format('Y');

            $ultimoNumero = Proposicao::withTrashed()
                ->where('camara_id', $proposicao->camara_id)
                ->where('tipo_proposicao_id', $proposicao->tipo_proposicao_id)
                ->where('ano', $ano)
                ->max('numero');

            $proposicao->numero = ($ultimoNumero ?? 0) + 1;
            $proposicao->ano = $ano;
            $proposicao->data_protocolo = $dataProtocolo;
            $proposicao->protocolado_por_id = $usuarioAutenticadoId;
            $proposicao->situacao = 'protocolada';
            $proposicao->save();
        });

        return to_route('proposicoes.show', $proposicao)
            ->with('success', 'Proposição protocolada com sucesso.');
    }
}
