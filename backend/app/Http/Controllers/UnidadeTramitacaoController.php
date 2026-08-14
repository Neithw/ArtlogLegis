<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnidadeTramitacaoRequest;
use App\Http\Requests\UpdateUnidadeTramitacaoRequest;
use App\Models\Camara;
use App\Models\Proposicao;
use App\Models\UnidadeTramitacao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UnidadeTramitacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $unidadesTramitacao = UnidadeTramitacao::query()
            ->with('camara:id,nome')
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->orderBy('nome')
            ->paginate(10);

        $tiposLabels = UnidadeTramitacao::TIPOS;

        return view('unidades-tramitacao.index', compact('unidadesTramitacao', 'tiposLabels', 'usuarioIsRoot'));
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

        $tiposLabels = UnidadeTramitacao::TIPOS;

        return view('unidades-tramitacao.create', compact('camaras', 'tiposLabels', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnidadeTramitacaoRequest $request): RedirectResponse
    {
        UnidadeTramitacao::create($request->validated());

        return to_route('unidades-tramitacao.index')
            ->with('success', 'Unidade de tramitação cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnidadeTramitacao $unidadeTramitacao): View
    {
        $unidadeTramitacao->load('camara');

        $tiposLabels = UnidadeTramitacao::TIPOS;

        return view('unidades-tramitacao.edit', compact('unidadeTramitacao', 'tiposLabels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnidadeTramitacaoRequest $request, UnidadeTramitacao $unidadeTramitacao): RedirectResponse
    {
        $unidadeTramitacao->update($request->validated());

        return to_route('unidades-tramitacao.index')
            ->with('success', 'Unidade de tramitação atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnidadeTramitacao $unidadeTramitacao): RedirectResponse
    {
        $possuiProposicaoAtiva = Proposicao::query()
            ->where('situacao', 'protocolada')
            ->whereHas(
                'ultimaTramitacao',
                fn($query) => $query
                    ->where('unidade_destino_id', $unidadeTramitacao->id)
            )
            ->exists();

        if ($possuiProposicaoAtiva) {
            throw ValidationException::withMessages([
                'unidadeTramitacao' => 'Esta unidade é o destino atual de uma ou mais proposições protocoladas e não pode ser arquivada.'
            ]);
        }

        $unidadeTramitacao->delete();

        return to_route('unidades-tramitacao.index')
            ->with('success', 'Unidade de tramitação arquivada com sucesso.');
    }

    public function arquivadas(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $unidadesTramitacao = UnidadeTramitacao::onlyTrashed()
            ->with('camara:id,nome')
            ->when(
                ! $usuarioIsRoot,
                fn($query) => $query
                    ->where('camara_id', $usuarioAutenticado->camara_id)
            )
            ->latest('deleted_at')
            ->paginate(10);

        $tiposLabels = UnidadeTramitacao::TIPOS;

        return view('unidades-tramitacao.arquivadas', compact('unidadesTramitacao', 'tiposLabels', 'usuarioIsRoot'));
    }

    public function restore(UnidadeTramitacao $unidadeTramitacao): RedirectResponse
    {
        abort_unless($unidadeTramitacao->trashed(), 404);

        $unidadeTramitacao->restore();

        return to_route('unidades-tramitacao.arquivadas')
            ->with('success', 'Unidade de tramitação restaurada com sucesso.');
    }
}
