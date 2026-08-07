<?php

use App\Http\Controllers\CamaraController;
use App\Http\Controllers\LegislaturaController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VereadorController;
use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Legislatura;
use App\Models\Partido;
use App\Models\User;
use App\Models\Vereador;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    EnsureUserIsActive::class,
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('/camaras', CamaraController::class)
        ->middlewareFor(['index', 'show'], 'can:camaras:visualizar')
        ->middlewareFor(['create', 'store'], 'can:camaras:criar')
        ->middlewareFor(['edit', 'update'], 'can:camaras:editar')
        ->middlewareFor('destroy', 'can:camaras:excluir');
    // -----------------------------------------------------------------------------------------------

    Route::resource('usuarios', UserController::class)
        ->parameters([
            'usuarios' => 'user',
        ])
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . User::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . User::class)
        ->middlewareFor(['edit', 'update'], 'can:update,user')
        ->middlewareFor(['destroy'], 'can:delete,user');

    Route::patch('/usuarios/{user}/desativar', [UserController::class, 'desativar'])
        ->name('usuarios.desativar')
        ->middleware('can:desativar,user');

    Route::patch('/usuarios/{user}/reativar', [UserController::class, 'reativar'])
        ->name('usuarios.reativar')
        ->middleware('can:reativar,user');
    // -----------------------------------------------------------------------------------------------

    Route::resource('vereadores', VereadorController::class)
        ->parameters([
            'vereadores' => 'vereador'
        ])
        ->middlewareFor('index', 'can:viewAny,' . Vereador::class)
        ->middlewareFor('show', 'can:view,vereador')
        ->middlewareFor(['create', 'store'], 'can:create,' . Vereador::class)
        ->middlewareFor(['edit', 'update'], 'can:update,vereador')
        ->middlewareFor('destroy', 'can:delete,vereador');
    // -----------------------------------------------------------------------------------------------

    Route::resource('legislaturas', LegislaturaController::class)
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . Legislatura::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . Legislatura::class)
        ->middlewareFor(['edit', 'update'], 'can:update,legislatura')
        ->middlewareFor('destroy', 'can:delete,legislatura');
    // -----------------------------------------------------------------------------------------------

    Route::resource('partidos', PartidoController::class)
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . Partido::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . Partido::class)
        ->middlewareFor(['edit', 'update'], 'can:update,partido')
        ->middlewareFor('destroy', 'can:delete,partido');

    Route::get('/partidos/arquivados', [PartidoController::class, 'arquivados'])
        ->middleware('can:viewArchived,' . Partido::class)
        ->name('partidos.arquivados');

    Route::patch('/partidos/{partido}/restaurar', [PartidoController::class, 'restore'])
        ->withTrashed()
        ->middleware('can:restore,partido')
        ->name('partidos.restore');
    // -----------------------------------------------------------------------------------------------
});
