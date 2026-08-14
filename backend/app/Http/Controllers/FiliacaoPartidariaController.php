<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFiliacaoPartidariaRequest;
use App\Models\Mandato;
use App\Models\Partido;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

        DB::transaction(function () use ($dadosValidados, $dataTroca, $mandato): void {
            $mandatoBloqueado = Mandato::query()
                ->whereKey($mandato->id)
                ->lockForUpdate()
                ->firstOrFail();

            $filiacaoAtual = $mandatoBloqueado
                ->filiacoesPartidarias()
                ->orderByDesc('data_inicio')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $filiacaoAtual->partido_id === (int) $dadosValidados['partido_id']) {
                throw ValidationException::withMessages([
                    'partido_id' => 'O novo partido deve ser diferente do partido atual.',
                ]);
            }

            if ($dataTroca->lte($filiacaoAtual->data_inicio)) {
                throw ValidationException::withMessages([
                    'data_troca' => 'A data da troca deve ser posterior ao início da filiação atual.',
                ]);
            }

            if ($mandatoBloqueado->data_fim && $dataTroca->gt($mandatoBloqueado->data_fim)) {
                throw ValidationException::withMessages([
                    'data_troca' => 'A data da troca não pode ser posterior ao término do mandato.',
                ]);
            }

            if ($filiacaoAtual->data_fim && $dataTroca->gt($filiacaoAtual->data_fim)) {
                throw ValidationException::withMessages([
                    'data_troca' => 'A data da troca não pode ser posterior ao término da filiação atual.',
                ]);
            }

            $filiacaoAtual->update([
                'data_fim' => $dataTroca
                    ->copy()
                    ->subDay()
            ]);

            $mandatoBloqueado->filiacoesPartidarias()->create([
                'partido_id' => $dadosValidados['partido_id'],
                'data_inicio' => $dataTroca,
                'data_fim' => $mandatoBloqueado->data_fim
            ]);
        });

        return to_route('mandatos.show', $mandato)
            ->with('success', 'Troca partidária registrada com sucesso.');
    }
}
