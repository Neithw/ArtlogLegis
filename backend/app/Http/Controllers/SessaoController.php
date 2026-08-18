<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessaoRequest;
use App\Http\Requests\UpdateSessaoRequest;
use App\Models\Camara;
use App\Models\Legislatura;
use App\Models\Sessao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $sessoes = Sessao::query()
            ->with(['camara:id,nome', 'legislatura:id,numero', 'criadoPor:id,name'])
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->where('camara_id', $usuarioAutenticado->camara_id))
            ->orderByDesc('data_hora_inicio_previsto')
            ->paginate(10);

        return view('sessoes.index', compact('sessoes', 'usuarioIsRoot'));
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
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->whereKey($usuarioAutenticado->camara_id))
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $legislaturas = Legislatura::query()
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->where('camara_id', $usuarioAutenticado->camara_id))
            ->orderBy('numero')
            ->get(['id', 'numero', 'camara_id']);

        $tipos = Sessao::TIPOS;

        return view('sessoes.create', compact('camaras', 'legislaturas', 'tipos', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessaoRequest $request): RedirectResponse
    {
        Sessao::create($request->validated());

        return to_route('sessoes.index')
            ->with('success', 'Sessão cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sessao $sessao): View
    {
        $sessao->load([
            'camara:id,nome',
            'legislatura:id,numero,data_inicio,data_fim',
            'criadoPor:id,name'
        ]);

        return view('sessoes.show', compact('sessao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sessao $sessao): View
    {
        $sessao->load(['camara:id,nome']);

        $legislaturas = Legislatura::query()
            ->where('camara_id', $sessao->camara_id)
            ->orderBy('numero')
            ->get(['id', 'numero']);

        $tipos = Sessao::TIPOS;

        return view('sessoes.edit', compact('sessao', 'legislaturas', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessaoRequest $request, Sessao $sessao): RedirectResponse
    {
        $sessao->update($request->validated());

        return to_route('sessoes.index')
            ->with('success', 'Sessão atualizada com sucesso.');
    }
}
