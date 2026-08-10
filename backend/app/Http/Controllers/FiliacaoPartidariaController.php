<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFiliacaoPartidariaRequest;
use App\Models\Mandato;
use App\Models\Partido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FiliacaoPartidariaController extends Controller
{
    public function create(Mandato $mandato): View|RedirectResponse
    {
        $mandato->load([
            'vereador',
            'legislatura.camara',
            'ultimaFiliacaoPartidaria.partido'
        ]);

        $filiacaoAtual = $mandato->ultimaFiliacaoPartidaria;

        if (! $filiacaoAtual) {
            return to_route('mandatos.index')
                ->with('error', 'Este mandato não possui uma filiação partidária registrada.');
        }

        $partidos = Partido::query()
            ->where('id', '!=', $filiacaoAtual->partido_id)
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'sigla'
            ]);

        return view('mandatos.troca-partidaria', compact('mandato', 'filiacaoAtual', 'partidos'));
    }

    public function store(StoreFiliacaoPartidariaRequest $request, Mandato $mandato): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $dataTroca = Carbon::parse($dadosValidados['data_troca']);

        DB::transaction(function () use ($dadosValidados, $dataTroca, $mandato) {
            $filiacaoAtual = $mandato
                ->ultimaFiliacaoPartidaria()
                ->firstOrFail();

            $filiacaoAtual->update([
                'data_fim' => $dataTroca
                    ->copy()
                    ->subDay()
            ]);

            $mandato->filiacoesPartidarias()->create([
                'partido_id' => $dadosValidados['partido_id'],
                'data_inicio' => $dataTroca,
                'data_fim' => $mandato->data_fim
            ]);
        });

        return to_route('mandatos.index')
            ->with('success', 'Troca partidária registrada com sucesso.');
    }
}
