<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCamaraRequest;
use App\Http\Requests\UpdateCamaraRequest;
use App\Models\Camara;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CamaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camaras = Camara::query()
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereKey($usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->paginate(10);

        return view('camaras.index', compact('camaras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('camaras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCamaraRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        Camara::create($dadosValidados);

        return to_route('camaras.index')
            ->with('success', 'Câmara cadastrada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Camara $camara): View
    {
        return view('camaras.edit', compact('camara'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCamaraRequest $request, Camara $camara): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $camara->update($dadosValidados);

        return to_route('camaras.index')
            ->with('success', 'Câmara atualizada com sucesso.');
    }

    public function desativar(Camara $camara): RedirectResponse
    {
        $camara->update([
            'ativo' => false
        ]);

        return to_route('camaras.index')
            ->with('success', 'Câmara desativada com sucesso.');
    }

    public function reativar(Camara $camara): RedirectResponse
    {
        $camara->update([
            'ativo' => true
        ]);

        return to_route('camaras.index')
            ->with('success', 'Câmara reativada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Camara $camara): RedirectResponse
    // {
    //     $camara->delete();

    //     return to_route('camaras.index')
    //         ->with('success', 'Câmara excluída com sucesso.');
    // }
}
