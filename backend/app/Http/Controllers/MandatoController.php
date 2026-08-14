<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMandatoRequest;
use App\Http\Requests\UpdateMandatoRequest;
use App\Models\Legislatura;
use App\Models\Mandato;
use App\Models\Partido;
use App\Models\Vereador;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MandatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $mandatos = Mandato::query()
            ->with([
                'vereador',
                'legislatura.camara',
            ])
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereRelation('legislatura', 'camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderBy('data_inicio')
            ->paginate(10);

        return view('mandatos.index', compact('mandatos', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $vereadores = Vereador::query()
            ->with('camara:id,nome')
            ->whereHas('camara', function ($query) {
                $query->where('ativo', true);
            })
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->where('camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderBy('nome')
            ->get();

        $legislaturas = Legislatura::query()
            ->with('camara:id,nome')
            ->whereHas('camara', function ($query) {
                $query->where('ativo', true);
            })
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->where('camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderByDesc('data_inicio')
            ->get();

        $partidos = Partido::query()
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'sigla'
            ]);

        return view('mandatos.create', compact('vereadores', 'legislaturas', 'partidos', 'usuarioIsRoot'));
    }

    public function show(Mandato $mandato): View
    {
        $mandato->load([
            'vereador',
            'legislatura.camara',

            'filiacoesPartidarias' => fn($query) => $query
                ->with('partido')
                ->orderBy('data_inicio')
                ->orderBy('id'),
        ]);

        return view('mandatos.show', compact('mandato'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMandatoRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $partidoId = $dadosValidados['partido_id'];

        unset($dadosValidados['partido_id']);

        DB::transaction(function () use ($dadosValidados, $partidoId) {
            $mandato = Mandato::create($dadosValidados);

            $mandato->filiacoesPartidarias()->create([
                'partido_id' => $partidoId,
                'data_inicio' => $mandato->data_inicio,
                'data_fim' => $mandato->data_fim
            ]);
        });

        return to_route('mandatos.index')
            ->with('success', 'Mandato cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Mandato $mandato): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $mandato->load([
            'vereador',
            'legislatura.camara',
            'ultimaFiliacaoPartidaria.partido'
        ]);

        return view('mandatos.edit', compact('mandato', 'usuarioIsRoot'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMandatoRequest $request, Mandato $mandato): RedirectResponse
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($mandato, $dadosValidados) {
            $primeiraFiliacao = $mandato->primeiraFiliacaoPartidaria;
            $ultimaFiliacao = $mandato->ultimaFiliacaoPartidaria;

            $mandato->update($dadosValidados);

            if ($primeiraFiliacao) {
                $primeiraFiliacao->update([
                    'data_inicio' => $mandato->data_inicio
                ]);
            }

            if ($ultimaFiliacao) {
                $ultimaFiliacao->update([
                    'data_fim' => $mandato->data_fim
                ]);
            }
        });


        return to_route('mandatos.show', $mandato)
            ->with('success', 'Mandato atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mandato $mandato): RedirectResponse
    {
        $mandato->delete();

        return to_route('mandatos.index')
            ->with('success', 'Mandato arquivado com sucesso.');
    }

    public function arquivados(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $arquivados = Mandato::onlyTrashed()
            ->with([
                'vereador',
                'legislatura.camara',
                'ultimaFiliacaoPartidaria.partido'
            ])
            ->when(! $usuarioIsRoot, function ($query) use ($usuarioAutenticado) {
                $query->whereRelation('legislatura', 'camara_id', $usuarioAutenticado->camara_id);
            })
            ->orderBy('data_inicio')
            ->paginate(10);

        return view('mandatos.arquivados', compact('arquivados', 'usuarioIsRoot'));
    }

    public function restore(Mandato $mandato): RedirectResponse
    {
        abort_unless($mandato->trashed(), 404);

        $mandato->restore();

        return to_route('mandatos.arquivados')
            ->with('success', 'Mandato restaurado com sucesso.');
    }
}
