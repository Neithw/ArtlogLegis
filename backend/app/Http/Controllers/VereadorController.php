<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVereadorRequest;
use App\Http\Requests\UpdateVereadorRequest;
use App\Models\Camara;
use App\Models\User;
use App\Models\Vereador;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VereadorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $vereadores = Vereador::query()
            ->with([
                'camara:id,nome',
                'user:id,name,email'
            ])
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->where(
                    'camara_id',
                    $usuarioAutenticado->camara_id
                );
            })
            ->orderBy('nome')
            ->paginate(10);

        return view('vereadores.index', compact('vereadores', 'usuarioIsRoot'));
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
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereKey($usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $usuariosDisponiveis = User::query()
            ->where('ativo', true)
            ->whereIn('camara_id', $camaras->pluck('id'))
            ->whereNotIn('id', Vereador::withTrashed()
                ->whereNotNull('user_id')
                ->select('user_id'))
            ->orderBy('name')
            ->get(['id', 'camara_id', 'name', 'email']);

        return view('vereadores.create', compact('camaras', 'usuariosDisponiveis', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVereadorRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        Vereador::create($dadosValidados);

        return to_route('vereadores.index')
            ->with('success', 'Vereador cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vereador $vereador): View
    {
        $vereador->load([
            'camara:id,nome',
            'user:id,name,email'
        ]);

        return view('vereadores.show', compact('vereador'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vereador $vereador): View
    {
        $vereador->load([
            'camara:id,nome',
            'user:id,name,email,ativo'
        ]);

        $usuariosDisponiveis = User::withTrashed()
            ->where('camara_id', $vereador->camara_id)
            ->where(function ($query) use ($vereador) {
                $query->where(function ($query) {
                    $query
                        ->where('ativo', true)
                        ->whereNull('deleted_at');
                });

                if ($vereador->user_id !== null) {
                    $query->orWhere('id', $vereador->user_id);
                }
            })
            ->whereNotIn(
                'id',
                Vereador::withTrashed()
                    ->where('id', '!=', $vereador->id)
                    ->whereNotNull('user_id')
                    ->select('user_id')
            )
            ->orderBy('name')
            ->get(['id', 'camara_id', 'name', 'email', 'ativo', 'deleted_at']);

        return view('vereadores.edit', compact('vereador', 'usuariosDisponiveis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVereadorRequest $request, Vereador $vereador): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $vereador->update($dadosValidados);

        return to_route('vereadores.index')
            ->with('success', 'Vereador atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vereador $vereador): RedirectResponse
    {
        if ($vereador->mandatos()->withTrashed()->exists()) {
            return to_route('vereadores.index')
                ->with('error', 'Não é possível excluir um vereador que possui mandatos vinculados.');
        }

        $vereador->delete();

        return to_route('vereadores.index')
            ->with('success', 'Vereador excluído com sucesso.');
    }
}
