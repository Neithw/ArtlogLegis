<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Camara;
use App\Models\Permissao;
use App\Models\Role;
use App\Models\UnidadeTramitacao;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private const PACOTES_PERMISSOES = [
        'gerente' => [
            'usuarios:visualizar',
            'usuarios:criar',
            'usuarios:editar',
            'usuarios:desativar',
            'usuarios:reativar',

            'vereadores:visualizar',
            'vereadores:criar',
            'vereadores:editar',
            'vereadores:excluir',
            'vereadores:restaurar',

            'legislaturas:visualizar',
            'legislaturas:criar',
            'legislaturas:editar',
            'legislaturas:excluir',
            'legislaturas:restaurar',

            'partidos:visualizar',

            'mandatos:visualizar',
            'mandatos:criar',
            'mandatos:editar',
            'mandatos:excluir',
            'mandatos:restaurar',

            'tipos-proposicao:visualizar',
            'tipos-proposicao:criar',
            'tipos-proposicao:editar',
            'tipos-proposicao:excluir',
            'tipos-proposicao:restaurar',

            'proposicoes:visualizar',
            'proposicoes:criar',
            'proposicoes:editar',
            'proposicoes:excluir',
            'proposicoes:restaurar',
            'proposicoes:protocolar',

            'unidades-tramitacao:visualizar',
            'unidades-tramitacao:criar',
            'unidades-tramitacao:editar',
            'unidades-tramitacao:excluir',
            'unidades-tramitacao:restaurar',

            'tramitacoes:visualizar',
            'tramitacoes:encaminhar',
            'tramitacoes:receber'
        ],

        'usuario_comum' => [
            'vereadores:visualizar',
            'legislaturas:visualizar',
            'partidos:visualizar',
            'mandatos:visualizar',
            'tipos-proposicao:visualizar',
            'proposicoes:visualizar',
            'unidades-tramitacao:visualizar',
            'tramitacoes:visualizar'
        ]
    ];

    private function montarPacotesPermissoes(Collection $permissoes, Collection $roles): array
    {
        $idsPermissoesPorCodigo = $permissoes->pluck('id', 'codigo');

        $pacotes = [];

        foreach ($roles as $role) {
            $codigosPermissoes = self::PACOTES_PERMISSOES[$role->codigo] ?? [];

            $pacotes[$role->id] = [];

            foreach ($codigosPermissoes as $codigoPermissao) {
                $permissaoId = $idsPermissoesPorCodigo->get($codigoPermissao);

                if ($permissaoId !== null) {
                    $pacotes[$role->id][] = (int) $permissaoId;
                }
            }
        }

        return $pacotes;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $usuarioAutenticado = request()->user();
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $usuarios = User::query()
            ->with([
                'camara:id,nome',
                'role:id,nome,codigo'
            ])
            ->when(
                ! $usuarioAutenticado->isRoot(),
                function ($query) use ($usuarioAutenticado) {
                    $query->where('camara_id', $usuarioAutenticado->camara_id);
                }
            )
            ->orderBy('name')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios', 'usuarioIsRoot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('usuarios.create', $this->dadosDoFormulario($request->user()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $dadosValidados = $request->validated();

        $permissionIds = $dadosValidados['permissoes'] ?? [];

        $unidadesTramitacaoIds = $dadosValidados['unidades_tramitacao'] ?? [];

        unset(
            $dadosValidados['permissoes'],
            $dadosValidados['unidades_tramitacao']
        );

        DB::transaction(function () use ($dadosValidados, $permissionIds, $unidadesTramitacaoIds): void {
            $user = User::create($dadosValidados);

            $user->permissoes()->sync($permissionIds);
            $user->unidadesTramitacao()->sync($unidadesTramitacaoIds);
        });

        return to_route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user): View
    {
        if ($user->isRoot()) {
            abort(403);
        }

        $usuarioAutenticado = $request->user();

        $permissoesSelecionadas = $user->permissoes()
            ->pluck('permissoes.id')
            ->map(fn($id) => (int) $id);

        if (! $usuarioAutenticado->isRoot()) {
            $usuarioAutenticado->loadMissing('permissoes');

            $idsPermissoesGerenciaveis = $usuarioAutenticado->permissoes
                ->pluck('id')
                ->map(fn($id) => (int) $id);

            $permissoesSelecionadas = $permissoesSelecionadas
                ->intersect($idsPermissoesGerenciaveis)
                ->values();
        }

        $unidadesTramitacaoSelecionadas = $user
            ->unidadesTramitacao()
            ->pluck('unidades_tramitacao.id')
            ->map(fn($id) => (int) $id)
            ->all();

        return view('usuarios.edit', [
            ...$this->dadosDoFormulario($usuarioAutenticado, $user),
            'user' => $user,
            'permissoesSelecionadas' => $permissoesSelecionadas->all(),
            'unidadesTramitacaoSelecionadas' => $unidadesTramitacaoSelecionadas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($user->isRoot()) {
            abort(403);
        }

        $dadosValidados = $request->validated();
        $usuarioAutenticado = $request->user();

        $permissionIds = collect($dadosValidados['permissoes'] ?? [])
            ->map(fn($id) => (int) $id);

        if (! $usuarioAutenticado->isRoot()) {
            $usuarioAutenticado->loadMissing('permissoes');

            $idsPermissoesGerenciaveis = $usuarioAutenticado->permissoes
                ->pluck('id')
                ->map(fn($id) => (int) $id);

            $idsPermissoesAtuais = $user->permissoes()
                ->pluck('permissoes.id')
                ->map(fn($id) => (int) $id);

            $idsPermissoesForaDoEscopo = $idsPermissoesAtuais
                ->diff($idsPermissoesGerenciaveis);

            $permissionIds = $permissionIds
                ->merge($idsPermissoesForaDoEscopo)
                ->unique()
                ->values();
        }

        $unidadesTramitacaoIds = $dadosValidados['unidades_tramitacao'] ?? [];

        unset(
            $dadosValidados['permissoes'],
            $dadosValidados['unidades_tramitacao']
        );

        if (empty($dadosValidados['password'])) {
            unset($dadosValidados['password']);
        }

        DB::transaction(function () use ($user, $dadosValidados, $permissionIds, $unidadesTramitacaoIds): void {
            $user->update($dadosValidados);

            $user->permissoes()->sync($permissionIds->all());
            $user->unidadesTramitacao()->sync($unidadesTramitacaoIds);
        });

        return to_route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function desativar(Request $request, User $user): RedirectResponse
    {
        $usuarioAutenticado = $request->user();

        if ($user->isRoot()) {
            abort(403);
        }

        if ($usuarioAutenticado->is($user)) {
            abort(403, 'Você não pode desativar a própria conta.');
        }

        $user->update([
            'ativo' => false
        ]);

        return to_route('usuarios.index')
            ->with('success', 'Usuário desativado com sucesso.');
    }

    public function reativar(User $user): RedirectResponse
    {
        if ($user->isRoot()) {
            abort(403);
        }

        $user->update([
            'ativo' => true
        ]);

        return to_route('usuarios.index')
            ->with('success', 'Usuário reativado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $usuarioAutenticado = $request->user();

        if ($user->isRoot()) {
            abort(403);
        }

        if ($usuarioAutenticado->is($user)) {
            abort(403);
        }

        DB::transaction(function () use ($user): void {
            $user->update(['ativo' => false]);
            $user->delete();
        });

        return to_route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    private function dadosDoFormulario(User $usuarioAutenticado, ?User $usuarioEditado = null): array
    {
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camarasQuery = Camara::query();

        if ($usuarioIsRoot) {
            $camarasQuery->where(function ($query) use ($usuarioEditado) {
                $query->where('ativo', true);

                if ($usuarioEditado?->camara_id !== null) {
                    $query->orWhere('id', $usuarioEditado->camara_id);
                }
            });
        } else {
            $camarasQuery
                ->where('ativo', true)
                ->whereKey($usuarioAutenticado->camara_id);
        }

        $camaras = $camarasQuery
            ->orderBy('nome')
            ->get([
                'id',
                'nome'
            ]);

        $unidadesTramitacao = UnidadeTramitacao::query()
            ->whereIn('camara_id', $camaras->pluck('id'))
            ->orderBy('nome')
            ->get([
                'id',
                'camara_id',
                'nome',
                'sigla'
            ]);

        $roles = Role::query()
            ->where('codigo', '!=', 'root')
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'codigo'
            ]);

        if ($usuarioIsRoot) {
            $permissoes = Permissao::query()
                ->orderBy('nome')
                ->get();
        } else {
            $usuarioAutenticado->loadMissing('permissoes');

            $permissoes = $usuarioAutenticado->permissoes
                ->sortBy('nome')
                ->values();
        }

        $permissoesPorModulo = $permissoes->groupBy(
            fn(Permissao $permissao): string =>
            explode(':', $permissao->codigo, 2)[0]
        );

        $pacotesPermissoes = $this->montarPacotesPermissoes($permissoes, $roles);

        return compact(
            'camaras',
            'roles',
            'permissoesPorModulo',
            'pacotesPermissoes',
            'usuarioIsRoot',
            'unidadesTramitacao'
        );
    }
}
