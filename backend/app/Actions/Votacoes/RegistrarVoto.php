<?php

namespace App\Actions\Votacoes;

use App\Models\ItemPauta;
use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\SessaoPresenca;
use App\Models\User;
use App\Models\Votacao;
use App\Models\Voto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrarVoto
{
    public function executar(Votacao $votacao, Mandato $mandato, User $registradoPor, string $escolha): Voto
    {
        if (! array_key_exists($escolha, Voto::ESCOLHAS)) {
            throw ValidationException::withMessages([
                'escolha' => 'O voto selecionado é inválido.'
            ]);
        }

        $sessaoId = $votacao
            ->itemPauta
            ->sessao_id;

        return DB::transaction(function () use ($votacao, $mandato, $registradoPor, $escolha, $sessaoId): Voto {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessaoId);

            $itemPauta = ItemPauta::query()
                ->whereKey($votacao->item_pauta_id)
                ->where('sessao_id', $sessao->id)
                ->lockForUpdate()
                ->firstOrFail();

            $votacaoBloqueada = Votacao::query()
                ->whereKey($votacao->id)
                ->where('item_pauta_id', $itemPauta->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($sessao->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'votacao' => 'A sessão precisa estar aberta para registrar votos.'
                ]);
            }

            if ($itemPauta->situacao !== 'em_votacao') {
                throw ValidationException::withMessages([
                    'votacao' => 'O item da pauta não está em votação.'
                ]);
            }

            if ($votacaoBloqueada->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'votacao' => 'Esta votação não está aberta.'
                ]);
            }

            $presenca = SessaoPresenca::query()
                ->where('sessao_id', $sessao->id)
                ->where('mandato_id', $mandato->id)
                ->lockForUpdate()
                ->first();

            if ($presenca === null || $presenca->situacao !== 'presente') {
                throw ValidationException::withMessages([
                    'votacao' => 'Somente mandatos com presença confirmada podem votar.'
                ]);
            }

            $voto = $votacaoBloqueada
                ->votos()
                ->where('mandato_id', $mandato->id)
                ->lockForUpdate()
                ->first();

            if ($voto !== null) {
                if ($voto->escolha === $escolha) {
                    return $voto;
                }

                $voto->escolha = $escolha;
                $voto->atualizado_por_id = $registradoPor->id;

                $voto->save();

                return $voto;
            }

            return $votacaoBloqueada->votos()->create([
                'mandato_id' => $mandato->id,
                'registrado_por_id' => $registradoPor->id,
                'escolha' => $escolha,
            ]);
        });
    }
}
