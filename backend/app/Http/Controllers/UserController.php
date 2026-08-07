<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Camara;
use App\Models\Permissao;
use App\Models\Role;
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

            'legislaturas:visualizar',
            'legislaturas:criar',
            'legislaturas:editar',
            'legislaturas:excluir',

            'partidos:visualizar'
        ],

        'usuario_comum' => [
            'vereadores:visualizar',
            'legislaturas:visualizar',
            'partidos:visualizar'
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

        $usuarios = User::query()
            ->with([
                'camara:id,nome',
                'role:id,nome'
            ])
            ->when(
                ! $usuarioAutenticado->isRoot(),
                function ($query) use ($usuarioAutenticado) {
                    $query->where('camara_id', $usuarioAutenticado->camara_id);
                }
            )
            ->orderBy('name')
            ->paginate(10);

        return view('usuarios.index', compact('usuarios'));
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
    public function store(StoreUserRequest $request)
    {
        $dadosValidados = $request->validated();

        $permissionIds = $dadosValidados['permissoes'] ?? [];

        unset($dadosValidados['permissoes']);

        DB::transaction(function () use ($dadosValidados, $permissionIds): void {
            $user = User::create($dadosValidados);

            $user->permissoes()->sync($permissionIds);
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

        $user->load([
            'permissoes:id,nome,codigo',
        ]);

        $permissoesSelecionadas = $user->permissoes()
            ->pluck('permissoes.id')
            ->map(fn($id) => (int) $id)
            ->all();

        return view('usuarios.edit', [
            ...$this->dadosDoFormulario($request->user()),
            'user' => $user,
            'permissoesSelecionadas' => $permissoesSelecionadas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $dadosValidados = $request->validated();
        $permissionIds = $dadosValidados['permissoes'] ?? [];

        unset($dadosValidados['permissoes']);

        if (empty($dadosValidados['password'])) {
            unset($dadosValidados['password']);
        }

        DB::transaction(function () use ($user, $dadosValidados, $permissionIds): void {
            $user->update($dadosValidados);

            $user->permissoes()->sync($permissionIds);
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

        $user->delete();

        return to_route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }

    private function dadosDoFormulario(User $usuarioAutenticado): array
    {
        $usuarioIsRoot = $usuarioAutenticado->isRoot();

        $camaras = Camara::query()
            ->where('ativo', true)
            ->when(
                ! $usuarioIsRoot,
                function ($query) use ($usuarioAutenticado) {
                    $query->whereKey($usuarioAutenticado->camara_id);
                }
            )
            ->orderBy('nome')
            ->get([
                'id',
                'nome'
            ]);

        $roles = Role::query()
            ->where('codigo', '!=', 'root')
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'codigo'
            ]);

        $permissoes = Permissao::query()
            ->orderBy('nome')
            ->get();

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
            'usuarioIsRoot'
        );
    }
}
