<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Camara;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $usuarioAutenticado = request()->user();
        $usuarioIsRoot = $usuarioAutenticado->hasRole('root');

        if (! $usuarioIsRoot && $usuarioAutenticado->camara_id === null) {
            abort(403);
        }

        $usuarios = User::query()
            ->with([
                'camara:id,nome',
                'role:id,nome'
            ])
            ->when(
                ! $usuarioIsRoot,
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
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->hasRole('root');

        if (! $usuarioIsRoot && $usuarioAutenticado->camara_id === null) {
            abort(403);
        }

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
            ->with(['permissions:id,nome,codigo'])
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'codigo'
            ]);

        $permissionsPorModulo = Permission::query()
            ->orderBy('codigo')
            ->get([
                'id',
                'nome',
                'codigo'
            ])
            ->groupBy(function (Permission $permission) {
                return explode(':', $permission->codigo, 2)[0];
            });

        $pacotesPermissoes = $roles->mapWithKeys(function (Role $role): array {
            return [
                (string) $role->id => $role->permissions
                    ->pluck('id')
                    ->map(fn($permissionId): string => (string) $permissionId)
                    ->values()
                    ->all()
            ];
        })->all();

        return view('usuarios.create', compact(
            'camaras',
            'roles',
            'permissionsPorModulo',
            'pacotesPermissoes',
            'usuarioIsRoot'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $dadosValidados = $request->validated();

        $permissionIds = $dadosValidados['permissions'] ?? [];

        unset($dadosValidados['permissions']);

        DB::transaction(function () use ($dadosValidados, $permissionIds): void {
            $user = User::create($dadosValidados);

            $user->permissions()->sync($permissionIds);
        });

        return to_route('usuarios.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user)
    {
        $usuarioAutenticado = $request->user();
        $usuarioIsRoot = $usuarioAutenticado->hasRole('root');

        if ($user->hasRole('root')) {
            abort(403);
        }

        if (! $usuarioIsRoot && (int) $user->camara_id !== (int) $usuarioAutenticado->camara_id) {
            abort(403);
        }

        $user->load([
            'permissions:id,nome,codigo',
        ]);

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
            ->with(['permissions:id,nome,codigo'])
            ->orderBy('nome')
            ->get([
                'id',
                'nome',
                'codigo'
            ]);

        $permissionsPorModulo = Permission::query()
            ->orderBy('codigo')
            ->get([
                'id',
                'nome',
                'codigo'
            ])
            ->groupBy(function (Permission $permission) {
                return explode(':', $permission->codigo, 2)[0];
            });

        $pacotesPermissoes = $roles->mapWithKeys(function (Role $role): array {
            return [
                (string) $role->id => $role->permissions
                    ->pluck('id')
                    ->map(fn($permissionId): string => (string) $permissionId)
                    ->values()
                    ->all()
            ];
        })->all();

        return view('usuarios.edit', compact(
            'user',
            'camaras',
            'roles',
            'permissionsPorModulo',
            'pacotesPermissoes',
            'usuarioIsRoot'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $dadosValidados = $request->validated();
        $permissionIds = $dadosValidados['permissions'] ?? [];

        unset($dadosValidados['permissions']);

        if (empty($dadosValidados['password'])) {
            unset($dadosValidados['password']);
        }

        DB::transaction(function () use ($user, $dadosValidados, $permissionIds): void {
            $user->update($dadosValidados);

            $user->permissions()->sync($permissionIds);
        });

        return to_route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
