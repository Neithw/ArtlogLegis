<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTramitacaoRequest;
use App\Models\Proposicao;
use App\Models\Tramitacao;
use App\Models\UnidadeTramitacao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TramitacaoController extends Controller
{
    public function show(Request $request, Proposicao $proposicao): View
    {
        $proposicao->load([
            'camara',
            'legislatura',
            'tipoProposicao',
            'autorMandato.vereador',
            'criadoPor',

            'tramitacoes' => fn($query) => $query
                ->with([
                    'unidadeOrigem',
                    'unidadeDestino',
                    'encaminhadoPor',
                    'recebidoPor'
                ])
                ->orderByDesc('data_encaminhamento')
                ->orderByDesc('id')
        ]);

        $tramitacaoPendente = $proposicao->tramitacoes
            ->first(
                fn($tramitacao) => $tramitacao->data_recebimento === null
            );

        $ultimaTramitacao = $proposicao->tramitacoes->first();

        $unidadeAtual = null;

        if ($ultimaTramitacao !== null) {
            $unidadeAtual = $tramitacaoPendente
                ? $tramitacaoPendente->unidadeOrigem
                : $ultimaTramitacao->unidadeDestino;
        }

        $unidadesDestino = collect();

        if (
            $proposicao->situacao === 'protocolada'
            && $tramitacaoPendente === null
            && $request->user()->can('encaminhar', $proposicao)
        ) {
            $unidadesDestino = UnidadeTramitacao::query()
                ->where('camara_id', $proposicao->camara_id)
                ->when(
                    $unidadeAtual !== null,
                    fn($query) => $query
                        ->whereKeyNot($unidadeAtual->id)
                )
                ->orderBy('nome')
                ->get(['id', 'nome', 'sigla', 'tipo']);
        }

        return view('tramitacoes.show', compact('proposicao', 'tramitacaoPendente', 'unidadeAtual', 'unidadesDestino'));
    }

    public function store(StoreTramitacaoRequest $request, Proposicao $proposicao): RedirectResponse
    {
        $dadosValidados = $request->validated();

        DB::transaction(function () use ($request, $proposicao, $dadosValidados) {
            $proposicaoBloqueada = Proposicao::query()
                ->whereKey($proposicao->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($proposicaoBloqueada->situacao !== 'protocolada') {
                throw ValidationException::withMessages([
                    'proposicao' => 'Somente proposições protocoladas podem tramitar.'
                ]);
            }

            $possuiTramitacaoPendente = $proposicaoBloqueada
                ->tramitacoes()
                ->whereNull('data_recebimento')
                ->exists();

            if ($possuiTramitacaoPendente) {
                throw ValidationException::withMessages([
                    'proposicao' => 'A proposição já possui uma tramitação pendente.'
                ]);
            }

            $ultimaTramitacao = $proposicaoBloqueada
                ->tramitacoes()
                ->orderByDesc('data_encaminhamento')
                ->orderByDesc('id')
                ->first();

            $unidadeOrigemId = $ultimaTramitacao?->unidade_destino_id;

            $unidadeDestinoId = (int) $dadosValidados['unidade_destino_id'];

            if ($unidadeOrigemId !== null && (int) $unidadeOrigemId === $unidadeDestinoId) {
                throw ValidationException::withMessages([
                    'unidade_destino_id' => 'A unidade de destino deve ser diferente da unidade atual.'
                ]);
            }

            $proposicaoBloqueada->tramitacoes()->create([
                'unidade_origem_id' => $unidadeOrigemId,
                'unidade_destino_id' => $unidadeDestinoId,
                'encaminhado_por_id' => $request->user()->id,
                'data_encaminhamento' => now(),
                'despacho' => $dadosValidados['despacho'] ?? null
            ]);
        });

        return to_route('proposicoes.tramitacao.show', $proposicao)
            ->with('success', 'Proposição encaminhada com sucesso.');
    }

    public function receber(Request $request, Tramitacao $tramitacao): RedirectResponse
    {
        $proposicaoId = DB::transaction(function () use ($request, $tramitacao) {
            $tramitacaoBloqueada = Tramitacao::query()
                ->whereKey($tramitacao->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tramitacaoBloqueada->data_recebimento !== null) {
                throw ValidationException::withMessages([
                    'tramitacao' => 'Esta tramitação foi recebida.'
                ]);
            }

            $tramitacaoBloqueada->update([
                'recebido_por_id' => $request()->user()->id,
                'data_recebimento' => now()
            ]);

            return (int) $tramitacaoBloqueada->proposicao->id;
        });

        return to_route('proposicoes.tramitacao.show', $proposicaoId)
            ->with('success', 'Recebimento da proposição confirmado com sucesso');
    }
}
