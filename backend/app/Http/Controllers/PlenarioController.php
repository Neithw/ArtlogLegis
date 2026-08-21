<?php

namespace App\Http\Controllers;

use App\Actions\Sessoes\RegistrarPresenca;
use App\Actions\Votacoes\RegistrarVoto as RegistrarVotoAction;
use App\Http\Requests\RegistrarProprioVotoRequest;
use App\Models\Mandato;
use App\Models\Sessao;
use App\Models\Votacao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlenarioController extends Controller
{
    public function index(Request $request): View
    {
        $vereador = $request
            ->user()
            ->vereador()
            ->first();

        abort_if(
            $vereador === null,
            403,
            'Seu usuário não está vinculado a um vereador.'
        );

        $mandatos = $vereador
            ->mandatos()
            ->get();

        $sessoes = collect();

        if ($mandatos->isNotEmpty()) {
            $sessoes = Sessao::query()
                ->with([
                    'camara:id,nome',
                    'legislatura:id,numero',
                ])
                ->where('camara_id', $vereador->camara_id)
                ->whereIn(
                    'legislatura_id',
                    $mandatos->pluck('legislatura_id')
                )
                ->whereIn(
                    'situacao',
                    ['convocada', 'aberta', 'suspensa', 'encerrada']
                )
                ->orderByRaw(
                    <<<'SQL'
                    CASE situacao
                        WHEN 'aberta' THEN 1
                        WHEN 'suspensa' THEN 2
                        WHEN 'convocada' THEN 3
                        WHEN 'encerrada' THEN 4
                        ELSE 5
                    END
                SQL
                )
                ->orderByDesc('data_hora_inicio_previsto')
                ->get()
                ->filter(
                    fn(Sessao $sessao) => $mandatos->contains(
                        fn(Mandato $mandato) =>
                        $mandato->legislatura_id
                            === $sessao->legislatura_id
                            && $mandato->estaVigenteEm(
                                $sessao->data_hora_inicio_previsto
                            )
                    )
                )
                ->take(20)
                ->values();
        }

        return view('plenario.index', compact(
            'vereador',
            'sessoes'
        ));
    }

    public function show(Request $request, Sessao $sessao): View
    {
        abort_unless(
            in_array(
                $sessao->situacao,
                ['convocada', 'aberta', 'suspensa', 'encerrada'],
                true
            ),
            403,
            'Esta sessão ainda não está disponível no plenário.'
        );

        $mandato = $this->localizarMandatoDoUsuario(
            $request->user()->id,
            $sessao
        );

        $sessao->load([
            'camara:id,nome',
            'legislatura:id,numero',
        ]);

        $presenca = $sessao
            ->presencas()
            ->where('mandato_id', $mandato->id)
            ->first();

        $votacoes = Votacao::query()
            ->with([
                'itemPauta:id,sessao_id,proposicao_id,ordem,situacao',

                'itemPauta.proposicao:id,tipo_proposicao_id,numero,ano,ementa,texto_integral',

                'itemPauta.proposicao.tipoProposicao:id,nome',

                'votos:id,votacao_id,mandato_id,escolha',
            ])
            ->whereHas(
                'itemPauta',
                fn($query) => $query
                    ->where('sessao_id', $sessao->id)
            )
            ->orderByDesc('aberta_em')
            ->get();

        $votacaoAberta = $votacoes->firstWhere('situacao', 'aberta');

        $votacoesFinalizadas = $votacoes
            ->whereIn('situacao', ['encerrada', 'cancelada'])
            ->sort(function (Votacao $primeira, Votacao $segunda): int {
                $comparacaoDaOrdem =
                    $primeira->itemPauta->ordem
                    <=> $segunda->itemPauta->ordem;

                if ($comparacaoDaOrdem !== 0) {
                    return $comparacaoDaOrdem;
                }

                $dataDaPrimeira =
                    $primeira->cancelada_em
                    ?? $primeira->encerrada_em;

                $dataDaSegunda =
                    $segunda->cancelada_em
                    ?? $segunda->encerrada_em;

                return ($dataDaPrimeira?->getTimestamp() ?? 0)
                    <=> ($dataDaSegunda?->getTimestamp() ?? 0);
            })
            ->values();

        $meuVoto = $votacaoAberta
            ?->votos
            ->firstWhere('mandato_id', $mandato->id);

        return view('plenario.show', compact(
            'sessao',
            'mandato',
            'presenca',
            'votacaoAberta',
            'meuVoto',
            'votacoesFinalizadas'
        ));
    }

    public function registrarVoto(RegistrarProprioVotoRequest $request, Votacao $votacao, RegistrarVotoAction $registrarVoto): RedirectResponse
    {
        $votacao->loadMissing('itemPauta.sessao');

        $sessao = $votacao->itemPauta->sessao;

        $mandato = $this->localizarMandatoDoUsuario(
            $request->user()->id,
            $sessao
        );

        $registrarVoto->executar(
            $votacao,
            $mandato,
            $request->user(),
            $request->validated('escolha')
        );

        return to_route('plenario.sessoes.show', $sessao)
            ->with('success', 'Seu voto foi registrado com sucesso.');
    }

    public function confirmarPresenca(Request $request, Sessao $sessao, RegistrarPresenca $registrarPresenca): RedirectResponse
    {
        $mandato = $this->localizarMandatoDoUsuario(
            $request->user()->id,
            $sessao
        );

        $registrarPresenca->executar(
            $sessao,
            $mandato,
            $request->user(),
            'presente'
        );

        return to_route('plenario.sessoes.show', $sessao)
            ->with('success', 'Sua presença foi confirmada com sucesso.');
    }

    private function localizarMandatoDoUsuario(int $usuarioId, Sessao $sessao): Mandato
    {
        $mandato = Mandato::query()
            ->with('vereador:id,user_id,nome,nome_parlamentar')
            ->where('legislatura_id', $sessao->legislatura_id)
            ->whereHas(
                'vereador',
                fn($query) => $query
                    ->where('user_id', $usuarioId)
                    ->where('camara_id', $sessao->camara_id)
            )
            ->vigenteEm($sessao->data_hora_inicio_previsto)
            ->first();

        abort_if(
            $mandato === null,
            403,
            'Você não possui um mandato válido para participar desta sessão.'
        );

        return $mandato;
    }
}
