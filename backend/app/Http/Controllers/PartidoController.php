<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartidoRequest;
use App\Http\Requests\UpdatePartidoRequest;
use App\Models\Partido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioIsRoot = $request->user()->isRoot();

        $partidos = Partido::query()
            ->orderBy('nome')
            ->paginate(10);

        return view('partidos.index', compact('partidos', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('partidos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartidoRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        Partido::create($dadosValidados);

        return to_route('partidos.index')
            ->with('success', 'Partido cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partido $partido): View
    {
        return view('partidos.edit', compact('partido'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartidoRequest $request, Partido $partido): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $partido->update($dadosValidados);

        return to_route('partidos.index')
            ->with('success', 'Partido atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partido $partido): RedirectResponse
    {
        $partido->delete();

        return to_route('partidos.index')
            ->with('success', 'Partido arquivado com sucesso.');
    }

    public function arquivados(): View
    {
        $arquivados = Partido::onlyTrashed()
            ->orderBy('nome')
            ->paginate(10);

        return view('partidos.arquivados', compact('arquivados'));
    }

    public function restore(Partido $partido): RedirectResponse
    {
        $partido->restore();

        return to_route('partidos.arquivados')
            ->with('success', 'Partido restaurado com sucesso.');
    }
}
