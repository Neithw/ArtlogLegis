<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarSessaoPresencaRequest;
use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\SessaoPresenca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessaoPresencaController extends Controller
{
    public function salvar(RegistrarSessaoPresencaRequest $request, Sessao $sessao, Mandato $mandato): RedirectResponse|JsonResponse
    {
        $dadosValidados = $request->validated();
        $usuarioAutenticadoId = $request->user()->id;

        $presenca = DB::transaction(function () use ($sessao, $mandato, $dadosValidados, $usuarioAutenticadoId) {
            $sessaoBloqueada = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if (
                ! in_array(
                    $sessaoBloqueada->situacao,
                    ['convocada', 'aberta', 'suspensa'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'presenca' => 'As presenças não podem ser alteradas na situação atual da sessão.'
                ]);
            }

            $presenca = SessaoPresenca::firstOrNew([
                'sessao_id' => $sessao->id,
                'mandato_id' => $mandato->id
            ]);

            if ($presenca->exists) {
                $presenca->atualizado_por_id = $usuarioAutenticadoId;
            } else {
                $presenca->registrado_por_id = $usuarioAutenticadoId;
            }

            $presenca->situacao = $dadosValidados['situacao'];
            $presenca->observacao = $dadosValidados['observacao'] ?? null;

            $presenca->save();

            return $presenca;
        });

        $presenca->load([
            'registradoPor:id,name',
            'atualizadoPor:id,name'
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
