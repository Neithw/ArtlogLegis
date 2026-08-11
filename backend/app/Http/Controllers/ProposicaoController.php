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
            'autorMandato.vereador'
        ]);

        return view('proposicoes.show', compact('proposicao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposicao $proposicao): View
    {
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

        $proposicao->update($dadosValidados);

        return to_route('proposicoes.show', $proposicao)
            ->with('success', 'Proposição atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposicao $proposicao): RedirectResponse
    {
        $proposicao->delete();

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
}
