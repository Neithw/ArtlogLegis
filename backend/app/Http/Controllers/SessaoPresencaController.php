<?php

namespace App\Http\Controllers;

use App\Actions\Sessoes\RegistrarPresenca;
use App\Http\Requests\RegistrarSessaoPresencaRequest;
use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\SessaoPresenca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SessaoPresencaController extends Controller
{
    public function salvar(RegistrarSessaoPresencaRequest $request, Sessao $sessao, Mandato $mandato, RegistrarPresenca $registrarPresenca): RedirectResponse|JsonResponse
    {
        $dadosValidados = $request->validated();

        $presenca = $registrarPresenca->executar(
            $sessao,
            $mandato,
            $request->user(),
            $dadosValidados['situacao'],
            $dadosValidados['observacao'] ?? null
        );

        $presenca->load([
            'registradoPor:id,name',
            'atualizadoPor:id,name',
        ]);

        if ($request->expectsJson()) {
            $totaisPorSituacao = $sessao->presencas()
                ->selectRaw('situacao, COUNT(*) as total')
                ->groupBy('situacao')
                ->pluck('total', 'situacao');

            $totalMandatos = Mandato::query()
                ->where('legislatura_id', $sessao->legislatura_id)
                ->whereHas(
                    'vereador',
                    fn($query) => $query
                        ->where('camara_id', $sessao->camara_id)
                )
                ->vigenteEm($sessao->data_hora_inicio_previsto)
                ->count();

            $totalRegistradas = (int) $totaisPorSituacao->sum();

            return response()->json([
                'message' => 'Presença salva com sucesso.',

                'presenca' => [
                    'mandato_id' => $mandato->id,
                    'situacao' => $presenca->situacao,
                    'rotulo' =>
                    SessaoPresenca::SITUACOES[$presenca->situacao]
                        ?? 'Não registrada',
                    'observacao' => $presenca->observacao,
                    'registrado_por' => $presenca->registradoPor->name,
                    'atualizado_por' => $presenca->atualizadoPor?->name
                ],

                'totais' => [
                    'mandatos' => $totalMandatos,
                    'presentes' => (int) ($totaisPorSituacao['presente'] ?? 0),
                    'ausentes' => (int) ($totaisPorSituacao['ausente'] ?? 0),
                    'justificadas' => (int) ($totaisPorSituacao['justificada'] ?? 0),
                    'nao_registradas' => max($totalMandatos - $totalRegistradas, 0),
                ]
            ]);
        }

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Presença salva com sucesso.');
    }
}
