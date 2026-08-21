<?php

namespace App\Actions\Sessoes;

use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\SessaoPresenca;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrarPresenca
{
    public function executar(Sessao $sessao, Mandato $mandato, User $registradoPor, string $situacao, ?string $observacao = null): SessaoPresenca
    {
        if (! array_key_exists($situacao, SessaoPresenca::SITUACOES)) {
            throw ValidationException::withMessages([
                'situacao' => 'A situação da presença é inválida.',
            ]);
        }

        return DB::transaction(function () use ($sessao, $mandato, $registradoPor, $situacao, $observacao): SessaoPresenca {
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
                    'presenca' => 'As presenças não podem ser alteradas na situação atual da sessão.',
                ]);
            }

            $mandatoValido = Mandato::query()
                ->whereKey($mandato->id)
                ->where(
                    'legislatura_id',
                    $sessaoBloqueada->legislatura_id
                )
                ->whereHas(
                    'vereador',
                    fn($query) => $query
                        ->where(
                            'camara_id',
                            $sessaoBloqueada->camara_id
                        )
                )
                ->vigenteEm(
                    $sessaoBloqueada->data_hora_inicio_previsto
                )
                ->lockForUpdate()
                ->first();

            if ($mandatoValido === null) {
                throw ValidationException::withMessages([
                    'presenca' => 'O mandato informado não pertence a esta sessão.',
                ]);
            }

            $presenca = SessaoPresenca::query()
                ->where('sessao_id', $sessaoBloqueada->id)
                ->where('mandato_id', $mandatoValido->id)
                ->lockForUpdate()
                ->first();

            if ($presenca === null) {
                $presenca = new SessaoPresenca([
                    'sessao_id' => $sessaoBloqueada->id,
                    'mandato_id' => $mandatoValido->id,
                ]);

                $presenca->registrado_por_id = $registradoPor->id;
            } else {
                $presenca->atualizado_por_id = $registradoPor->id;
            }

            $presenca->situacao = $situacao;
            $presenca->observacao = $observacao;
            $presenca->save();

            return $presenca;
        });
    }
}
