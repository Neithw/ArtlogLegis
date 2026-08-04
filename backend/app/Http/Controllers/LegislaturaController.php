<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLegislaturaRequest;
use App\Http\Requests\UpdateLegislaturaRequest;
use App\Models\Camara;
use App\Models\Legislatura;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegislaturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $legislaturas = Legislatura::query()
            ->with(['camara:id,nome'])
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->where(
                    'camara_id',
                    $usuarioAutenticado->camara_id
                );
            })
            ->orderByDesc('data_inicio')
            ->paginate(10);

        return view('legislaturas.index', compact('legislaturas', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camaras = Camara::query()
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereKey($usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return view('legislaturas.create', compact('camaras', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLegislaturaRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        Legislatura::create($dadosValidados);

        return to_route('legislaturas.index')
            ->with('success', 'Legislatura cadastrada com sucesso');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Legislatura $legislatura): View
    {
        $legislatura->load(['camara:id,nome']);

        return view('legislaturas.edit', compact('legislatura'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegislaturaRequest $request, Legislatura $legislatura): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $legislatura->update($dadosValidados);

        return to_route('legislaturas.index')
            ->with('success', 'Legislatura atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Legislatura $legislatura): RedirectResponse
    {
        $legislatura->delete();

        return to_route('legislaturas.index')
            ->with('success', 'Legislatura excluída com sucesso.');
    }
}
