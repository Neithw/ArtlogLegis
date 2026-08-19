<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelarSessaoRequest;
use App\Http\Requests\StoreSessaoRequest;
use App\Http\Requests\SuspenderSessaoRequest;
use App\Http\Requests\UpdateSessaoRequest;
use App\Models\Camara;
use App\Models\Legislatura;
use App\Models\Proposicao;
use App\Models\Sessao;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $sessoes = Sessao::query()
            ->with(['camara:id,nome', 'legislatura:id,numero', 'criadoPor:id,name'])
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->where('camara_id', $usuarioAutenticado->camara_id))
            ->orderByDesc('data_hora_inicio_previsto')
            ->paginate(10);

        return view('sessoes.index', compact('sessoes', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camaras = Camara::query()
            ->where('ativo', true)
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->whereKey($usuarioAutenticado->camara_id))
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $legislaturas = Legislatura::query()
            ->when(! $usuarioIsRoot, fn($query) => $query
                ->where('camara_id', $usuarioAutenticado->camara_id))
            ->orderBy('numero')
            ->get(['id', 'numero', 'camara_id']);

        $tipos = Sessao::TIPOS;

        return view('sessoes.create', compact('camaras', 'legislaturas', 'tipos', 'usuarioIsRoot'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSessaoRequest $request): RedirectResponse
    {
        Sessao::create($request->validated());

        return to_route('sessoes.index')
            ->with('success', 'Sessão cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Sessao $sessao): View
    {
        $sessao->load([
            'camara:id,nome',
            'legislatura:id,numero,data_inicio,data_fim',
            'criadoPor:id,name',

            'itensPauta.proposicao:id,tipo_proposicao_id,numero,ano,ementa',
            'itensPauta.proposicao.tipoProposicao:id,nome',
            'itensPauta.incluidoPor:id,name',

            'eventos' => fn($query) => $query
                ->with('executadoPor:id,name')
                ->orderByDesc('created_at')
        ]);

        $podeGerenciarPauta =
            $sessao->situacao === 'em_preparacao'
            && $request->user()->can('gerenciarPauta', $sessao);

        $proposicoesDisponiveis = collect();

        if ($podeGerenciarPauta) {
            $proposicoesDisponiveis = Proposicao::query()
                ->with('tipoProposicao:id,nome')
                ->where('camara_id', $sessao->camara_id)
                ->where('legislatura_id', $sessao->legislatura_id)
                ->where('situacao', 'protocolada')
                ->whereDoesntHave(
                    'itensPauta',
                    fn($query) => $query
                        ->where('sessao_id', $sessao->id)
                )
                ->orderByDesc('ano')
                ->orderBy('numero')
                ->get([
                    'id',
                    'tipo_proposicao_id',
                    'numero',
                    'ano',
                    'ementa'
                ]);
        }

        return view('sessoes.show', compact('sessao', 'podeGerenciarPauta', 'proposicoesDisponiveis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sessao $sessao): View
    {
        abort_unless($sessao->situacao === 'em_preparacao', 403);

        $sessao->load(['camara:id,nome']);

        $legislaturas = Legislatura::query()
            ->where('camara_id', $sessao->camara_id)
            ->orderBy('numero')
            ->get(['id', 'numero']);

        $tipos = Sessao::TIPOS;

        return view('sessoes.edit', compact('sessao', 'legislaturas', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSessaoRequest $request, Sessao $sessao): RedirectResponse
    {
        DB::transaction(function () use ($request, $sessao) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            abort_unless($sessao->situacao === 'em_preparacao', 403);

            $sessao->update($request->validated());
        });

        return to_route('sessoes.index')
            ->with('success', 'Sessão atualizada com sucesso.');
    }

    public function convocar(Request $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'em_preparacao') {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões em preparação podem ser convocadas.'
                ]);
            }

            $legislatura = Legislatura::query()
                ->whereKey($sessao->legislatura_id)
                ->where('camara_id', $sessao->camara_id)
                ->lockForUpdate()
                ->first();

            if (! $legislatura) {
                throw ValidationException::withMessages([
                    'sessao' => 'A legislatura vinculada à sessão não está disponível.'
                ]);
            }

            $dataSessao = $sessao->data_hora_inicio_previsto;

            $dataForaDoPeriodo =
                $dataSessao->lt($legislatura->data_inicio->copy()->startOfDay())
                || $dataSessao->gt($legislatura->data_fim->copy()->endOfDay());

            if ($dataForaDoPeriodo) {
                throw ValidationException::withMessages([
                    'sessao' => 'A data da sessão não está dentro do período da legislatura.'
                ]);
            }

            $ano = $dataSessao->year;

            $ultimoNumero = Sessao::query()
                ->where('legislatura_id', $sessao->legislatura_id)
                ->where('tipo', $sessao->tipo)
                ->where('ano', $ano)
                ->max('numero');

            $proximoNumero = ($ultimoNumero ?? 0) + 1;
            $sessao->numero = $proximoNumero;
            $sessao->ano = $ano;
            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'convocada';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'convocar',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'convocada'
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão convocada com sucesso.');
    }

    public function abrir(Request $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'convocada') {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões convocadas podem ser abertas.'
                ]);
            }

            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'aberta';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'abrir',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'aberta'
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão aberta com sucesso.');
    }

    public function suspender(SuspenderSessaoRequest $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;
        $observacao = $request->validated('observacao');

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId, $observacao) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões abertas podem ser suspensas.'
                ]);
            }

            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'suspensa';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'suspender',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'suspensa',
                'observacao' => $observacao
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão suspensa com sucesso.');
    }

    public function retomar(Request $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'suspensa') {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões suspensas podem ser retomadas.'
                ]);
            }

            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'aberta';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'retomar',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'aberta',
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão retomada com sucesso.');
    }

    public function encerrar(Request $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            if ($sessao->situacao !== 'aberta') {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões abertas podem ser encerradas.'
                ]);
            }

            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'encerrada';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'encerrar',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'encerrada',
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão encerrada com sucesso.');
    }

    public function cancelar(CancelarSessaoRequest $request, Sessao $sessao): RedirectResponse
    {
        $usuarioAutenticadoId = $request->user()->id;
        $observacao = $request->validated('observacao');

        DB::transaction(function () use ($sessao, $usuarioAutenticadoId, $observacao) {
            $sessao = Sessao::query()
                ->lockForUpdate()
                ->findOrFail($sessao->id);

            $situacoesPermitidas = ['em_preparacao', 'convocada'];
            if (! in_array($sessao->situacao, $situacoesPermitidas, true)) {
                throw ValidationException::withMessages([
                    'sessao' => 'Somente sessões em preparação ou convocadas podem ser canceladas.'
                ]);
            }

            $situacaoAnterior = $sessao->situacao;
            $sessao->situacao = 'cancelada';
            $sessao->save();

            $sessao->eventos()->create([
                'executado_por_id' => $usuarioAutenticadoId,
                'acao' => 'cancelar',
                'situacao_anterior' => $situacaoAnterior,
                'situacao_nova' => 'cancelada',
                'observacao' => $observacao
            ]);
        });

        return to_route('sessoes.show', $sessao)
            ->with('success', 'Sessão cancelada com sucesso.');
    }
}
