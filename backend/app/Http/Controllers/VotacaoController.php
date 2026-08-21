<?php

namespace App\Http\Controllers;

use App\Actions\Votacoes\RegistrarVoto as RegistrarVotoAction;
use App\Http\Requests\AbrirVotacaoRequest;
use App\Http\Requests\CancelarVotacaoRequest;
use App\Http\Requests\RegistrarVotoRequest;
use App\Models\ItemPauta;
use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\Votacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class VotacaoController extends Controller
{
    public function abrir(AbrirVotacaoRequest $request, ItemPauta $itemPauta): RedirectResponse
    {
        Gate::authorize('abrir', [Votacao::class, $itemPauta]);

        DB::transaction(function () use ($request, $itemPauta): void {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($itemPauta->sessao_id);

            if ($sessao->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'votacao' => 'A sessão precisa estar aberta para iniciar uma votação.'
                ]);
            }

            $itemPautaBloqueado = ItemPauta::query()
                ->lockForUpdate()
                ->findOrFail($itemPauta->id);

            if ($itemPautaBloqueado->situacao !== 'pendente') {
                throw ValidationException::withMessages([
                    'votacao' => 'Somente itens pendentes podem ter uma votação aberta.'
                ]);
            }

            $existeVotacaoAbertaNaSessao = Votacao::query()
                ->where('situacao', 'aberta')
                ->whereHas(
                    'itemPauta',
                    fn($query) => $query
                        ->where('sessao_id', $sessao->id)
                )
                ->exists();

            if ($existeVotacaoAbertaNaSessao) {
                throw ValidationException::withMessages([
                    'votacao' => 'Já existe uma votação aberta nesta sessão. Encerre-a ou cancele-a antes de abrir outra.',
                ]);
            }

            $dadosValidados = $request->validated();

            $itemPautaBloqueado->votacoes()->create([
                'aberta_por_id' => $request->user()->id,
                'tipo' => $dadosValidados['tipo'],
                'criterio_aprovacao' => $dadosValidados['criterio_aprovacao'],
                'aberta_em' => now(),
                'observacao' => $dadosValidados['observacao'] ?? null
            ]);

            $itemPautaBloqueado->situacao = 'em_votacao';
            $itemPautaBloqueado->save();
        });

        return back()
            ->with('success', 'Votação aberta com sucesso.');
    }

    public function registrarVoto(RegistrarVotoRequest $request, Votacao $votacao, RegistrarVotoAction $registrarVoto): RedirectResponse
    {
        Gate::authorize('registrarVoto', $votacao);

        $dadosValidados = $request->validated();

        $mandato = Mandato::query()
            ->findOrFail($dadosValidados['mandato_id']);

        $registrarVoto->executar(
            $votacao,
            $mandato,
            $request->user(),
            $dadosValidados['escolha']
        );

        return back()->with(
            'success',
            'Voto salvo com sucesso.'
        );
    }

    public function encerrar(Request $request, Votacao $votacao): RedirectResponse
    {
        Gate::authorize('encerrar', $votacao);

        $sessaoId = $votacao
            ->itemPauta
            ->sessao_id;

        DB::transaction(function () use ($request, $votacao, $sessaoId): void {
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
                    'votacao' => 'A sessão precisa estar aberta para encerrar a votação.'
                ]);
            }

            if ($itemPauta->situacao !== 'em_votacao') {
                throw ValidationException::withMessages([
                    'votacao' => 'O item da pauta não está em votação.'
                ]);
            }

            if ($votacaoBloqueada->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'votacao' => 'Somente votações abertas podem ser encerradas.'
                ]);
            }

            $votos = $votacaoBloqueada
                ->votos()
                ->lockForUpdate()
                ->get();

            if ($votos->isEmpty()) {
                throw ValidationException::withMessages([
                    'votacao' => 'Não é possível encerrar uma votação sem votos registrados.'
                ]);
            }

            $favoraveis = $votos
                ->where('escolha', 'favoravel')
                ->count();

            $contrarios = $votos
                ->where('escolha', 'contrario')
                ->count();

            $resultado = match (true) {
                $favoraveis === 0 && $contrarios === 0 => 'sem_decisao',
                $favoraveis > $contrarios => 'aprovada',
                $contrarios > $favoraveis => 'rejeitada',
                default => 'empate',
            };

            $votacaoBloqueada->situacao = 'encerrada';
            $votacaoBloqueada->resultado = $resultado;
            $votacaoBloqueada->encerrada_por_id = $request->user()->id;
            $votacaoBloqueada->encerrada_em = now();

            $votacaoBloqueada->save();

            $itemPauta->situacao = 'votado';
            $itemPauta->save();
        });

        return back()->with(
            'success',
            'Votação encerrada e resultado apurado com sucesso.'
        );
    }

    public function cancelar(CancelarVotacaoRequest $request, Votacao $votacao): RedirectResponse
    {
        Gate::authorize('cancelar', $votacao);

        $sessaoId = $votacao
            ->itemPauta
            ->sessao_id;

        DB::transaction(function () use ($request, $votacao, $sessaoId): void {
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
                    'votacao' => 'A sessão precisa estar aberta para cancelar a votação.'
                ]);
            }

            if ($itemPauta->situacao !== 'em_votacao') {
                throw ValidationException::withMessages([
                    'votacao' => 'O item da pauta não está em votação.'
                ]);
            }

            if ($votacaoBloqueada->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'votacao' => 'Somente votações abertas podem ser canceladas.'
                ]);
            }

            $votacaoBloqueada->situacao = 'cancelada';
            $votacaoBloqueada->resultado = null;
            $votacaoBloqueada->cancelada_por_id = $request->user()->id;
            $votacaoBloqueada->cancelada_em = now();
            $votacaoBloqueada->motivo_cancelamento = $request->validated('motivo_cancelamento');

            $votacaoBloqueada->save();

            $itemPauta->situacao = 'pendente';
            $itemPauta->save();
        });

        return back()
            ->with('success', 'Votação cancelada com sucesso.');
    }
}
