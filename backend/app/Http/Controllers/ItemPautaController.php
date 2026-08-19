<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoverItemPautaRequest;
use App\Http\Requests\StoreItemPautaRequest;
use App\Models\ItemPauta;
use App\Models\Sessao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemPautaController extends Controller
{
    public function store(StoreItemPautaRequest $request, Sessao $sessao): RedirectResponse
    {
        $proposicaoId = (int) $request->validated('proposicao_id');
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($sessao, $proposicaoId, $usuarioAutenticadoId) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'em_preparacao') {
                throw ValidationException::withMessages([
                    'pauta' => 'A pauta somente pode ser alterada enquanto a sessão está em preparação.'
                ]);
            }

            $proposicaoJaIncluida = $sessao->itensPauta()
                ->where('proposicao_id', $proposicaoId)
                ->exists();

            if ($proposicaoJaIncluida) {
                throw ValidationException::withMessages([
                    'proposicao_id' => 'Esta proposição já está incluída na pauta da sessão.'
                ]);
            }

            $ultimaOrdem = $sessao->itensPauta()
                ->max('ordem');

            $proximaOrdem = ($ultimaOrdem ?? 0) + 1;

            $sessao->itensPauta()->create([
                'proposicao_id' => $proposicaoId,
                'incluido_por_id' => $usuarioAutenticadoId,
                'ordem' => $proximaOrdem
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Proposição incluída na pauta com sucesso.');
    }

    public function destroy(Sessao $sessao, ItemPauta $itemPauta): RedirectResponse
    {
        DB::transaction(function () use ($sessao, $itemPauta) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'em_preparacao') {
                throw ValidationException::withMessages([
                    'pauta' => 'A pauta somente pode ser alterada enquanto a sessão está em preparação.'
                ]);
            }

            $itemPauta = $sessao->itensPauta()
                ->whereKey($itemPauta->id)
                ->lockForUpdate()
                ->firstOrFail();

            $ordemRemovida = $itemPauta->ordem;

            $itemPauta->delete();

            $itensPosteriores = $sessao->itensPauta()
                ->where('ordem', '>', $ordemRemovida)
                ->lockForUpdate()
                ->get();

            foreach ($itensPosteriores as $itemPosterior) {
                $itemPosterior->ordem--;
                $itemPosterior->save();
            }
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Proposição removida da pauta com sucesso.');
    }

    public function mover(MoverItemPautaRequest $request, Sessao $sessao, ItemPauta $itemPauta): RedirectResponse|JsonResponse
    {
        $direcao = $request->validated('direcao');

        $ordemAtual = DB::transaction(function () use ($sessao, $itemPauta, $direcao) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'em_preparacao') {
                throw ValidationException::withMessages([
                    'pauta' => 'A pauta somente pode ser alterada enquanto a sessão está em preparação.'
                ]);
            }

            $itemPauta = $sessao->itensPauta()
                ->whereKey($itemPauta->id)
                ->lockForUpdate()
                ->firstOrFail();

            $consultaVizinho = $sessao->itensPauta()
                ->reorder()
                ->lockForUpdate();

            if ($direcao === 'acima') {
                $itemVizinho = $consultaVizinho
                    ->where('ordem', '<', $itemPauta->ordem)
                    ->orderByDesc('ordem')
                    ->first();
            } else {
                $itemVizinho = $consultaVizinho
                    ->where('ordem', '>', $itemPauta->ordem)
                    ->orderBy('ordem')
                    ->first();
            }

            if (! $itemVizinho) {
                throw ValidationException::withMessages([
                    'pauta' => 'O item não pode ser movido nessa direção.'
                ]);
            }

            $ordemOriginal = $itemPauta->ordem;
            $ordemVizinho = $itemVizinho->ordem;

            $ordemTemporaria = ((int) $sessao->itensPauta()->max('ordem')) + 1;

            $itemPauta->update(['ordem' => $ordemTemporaria]);

            $itemVizinho->update(['ordem' => $ordemOriginal]);

            $itemPauta->update(['ordem' => $ordemVizinho]);

            return $sessao->itensPauta()
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Ordem da pauta atualizada com sucesso.',
                'ordem' => $ordemAtual
            ]);
        }

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Ordem da pauta atualizada com sucesso.');
    }
}
