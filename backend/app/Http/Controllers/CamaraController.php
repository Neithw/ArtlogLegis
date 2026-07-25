<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCamaraRequest;
use App\Http\Requests\UpdateCamaraRequest;
use App\Models\Camara;

class CamaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $camaras = Camara::orderBy('nome')
            ->paginate(10);

        return view('camaras.index', compact('camaras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('camaras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCamaraRequest $request)
    {
        $dadosValidados = $request->validated();

        Camara::create($dadosValidados);

        return to_route('camaras.index')
            ->with('success', 'Câmara cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Camara $camara)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Camara $camara)
    {
        return view('camaras.edit', compact('camara'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCamaraRequest $request, Camara $camara)
    {
        $dadosValidados = $request->validated();

        $camara->update($dadosValidados);

        return to_route('camaras.index')
            ->with('success', 'Câmara atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Camara $camara)
    {
        $camara->delete();

        return to_route('camaras.index')
            ->with('success', 'Câmara excluída com sucesso.');
    }
}
