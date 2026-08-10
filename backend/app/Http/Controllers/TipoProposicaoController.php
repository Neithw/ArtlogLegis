<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTipoProposicaoRequest;
use App\Http\Requests\UpdateTipoProposicaoRequest;
use App\Models\Camara;
use App\Models\TipoProposicao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TipoProposicaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $tiposProposicao = TipoProposicao::query()
            ->with('camara:id,nome')
            ->when(! $usuarioAutenticado->isRoot(), function ($query) use ($usuarioAutenticado) {
                $query->where('camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->paginate(10);

        return view('tipos-proposicao.index', compact('tiposProposicao', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $request->user()->isRoot();

        $camaras = Camara::query()
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereKey($usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('tipos-proposicao.create', compact('camaras', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoProposicaoRequest $request): RedirectResponse
    {
        TipoProposicao::create($request->validated());

        return to_route('tipos-proposicao.index')
            ->with('success', 'Tipo de proposição cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoProposicao $tipoProposicao): View
    {
        return view('tipos-proposicao.edit', compact('tipoProposicao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoProposicaoRequest $request, TipoProposicao $tipoProposicao): RedirectResponse
    {
        $tipoProposicao->update($request->validated());

        return to_route('tipos-proposicao.index')
            ->with('success', 'Tipo de proposição atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoProposicao $tipoProposicao): RedirectResponse
    {
        $tipoProposicao->delete();

        return to_route('tipos-proposicao.index')
            ->with('success', 'Tipo de proposição arquivado com sucesso.');
    }

    public function arquivados(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $request->user()->isRoot();

        $tiposProposicao = TipoProposicao::onlyTrashed()
            ->with('camara:id,nome')
            ->when(! $usuarioAutenticado->isRoot(), function ($query) use ($usuarioAutenticado) {
                $query->where('camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->paginate(10);

        return view('tipos-proposicao.arquivados', compact('tiposProposicao', 'usuarioIsRoot'));
    }

    public function restore(TipoProposicao $tipoProposicao): RedirectResponse
    {
        $tipoProposicao->restore();

        return to_route('tipos-proposicao.index')
            ->with('success', 'Tipo de proposição restaurado com sucesso.');
    }
}
