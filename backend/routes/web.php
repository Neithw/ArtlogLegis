<?php

use App\Http\Controllers\CamaraController;
use App\Http\Controllers\FiliacaoPartidariaController;
use App\Http\Controllers\LegislaturaController;
use App\Http\Controllers\MandatoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\ProposicaoController;
use App\Http\Controllers\TipoProposicaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VereadorController;
use App\Http\Middleware\EnsureUserHasActiveCamara;
use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Camara;
use App\Models\Legislatura;
use App\Models\Mandato;
use App\Models\Partido;
use App\Models\Proposicao;
use App\Models\TipoProposicao;
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
    EnsureUserHasActiveCamara::class,
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::patch('/camaras/{camara}/desativar', [CamaraController::class, 'desativar'])
        ->name('camaras.desativar')
        ->middleware('can:desativar,camara');

    Route::patch('/camaras/{camara}/reativar', [CamaraController::class, 'reativar'])
        ->name('camaras.reativar')
        ->middleware('can:reativar,camara');

    Route::resource('/camaras', CamaraController::class)
        ->except(['show', 'destroy'])
        ->middlewareFor('index', 'can:viewAny,' . Camara::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . Camara::class)
        ->middlewareFor(['edit', 'update'], 'can:update,camara');
    // ->middlewareFor('destroy', 'can:delete,camara');
    // -----------------------------------------------------------------------------------------------

    Route::patch('/usuarios/{user}/desativar', [UserController::class, 'desativar'])
        ->name('usuarios.desativar')
        ->middleware('can:desativar,user');

    Route::patch('/usuarios/{user}/reativar', [UserController::class, 'reativar'])
        ->name('usuarios.reativar')
        ->middleware('can:reativar,user');

    Route::resource('usuarios', UserController::class)
        ->parameters([
            'usuarios' => 'user',
        ])
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . User::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . User::class)
        ->middlewareFor(['edit', 'update'], 'can:update,user')
        ->middlewareFor(['destroy'], 'can:delete,user');
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

    Route::get('/partidos/arquivados', [PartidoController::class, 'arquivados'])
        ->middleware('can:viewArchived,' . Partido::class)
        ->name('partidos.arquivados');

    Route::patch('/partidos/{partido}/restaurar', [PartidoController::class, 'restore'])
        ->withTrashed()
        ->middleware('can:restore,partido')
        ->name('partidos.restore');

    Route::resource('partidos', PartidoController::class)
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . Partido::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . Partido::class)
        ->middlewareFor(['edit', 'update'], 'can:update,partido')
        ->middlewareFor('destroy', 'can:delete,partido');
    // -----------------------------------------------------------------------------------------------

    Route::get('/mandatos/arquivados', [MandatoController::class, 'arquivados'])
        ->middleware('can:viewArchived,' . Mandato::class)
        ->name('mandatos.arquivados');

    Route::patch('/mandatos/{mandato}/restaurar', [MandatoController::class, 'restore'])
        ->withTrashed()
        ->middleware('can:restore,mandato')
        ->name('mandatos.restore');

    Route::resource('mandatos', MandatoController::class)
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . Mandato::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . Mandato::class)
        ->middlewareFor(['edit', 'update'], 'can:update,mandato')
        ->middlewareFor('destroy', 'can:delete,mandato');
    // -----------------------------------------------------------------------------------------------

    Route::get('/mandatos/{mandato}/troca-partidaria', [FiliacaoPartidariaController::class, 'create'])
        ->middleware('can:update,mandato')
        ->name('mandatos.troca-partidaria.create');

    Route::post('/mandatos/{mandato}/troca-partidaria', [FiliacaoPartidariaController::class, 'store'])
        ->middleware('can:update,mandato')
        ->name('mandatos.troca-partidaria.store');
    // -----------------------------------------------------------------------------------------------

    Route::get('/tipos-proposicao/arquivados', [TipoProposicaoController::class, 'arquivados'])
        ->middleware('can:viewArchived,' . TipoProposicao::class)
        ->name('tipos-proposicao.arquivados');

    Route::patch('/tipos-proposicao/{tipoProposicao}/restaurar', [TipoProposicaoController::class, 'restore'])
        ->withTrashed()
        ->middleware('can:restore,tipoProposicao')
        ->name('tipos-proposicao.restore');

    Route::resource('tipos-proposicao', TipoProposicaoController::class)
        ->parameters([
            'tipos-proposicao' => 'tipoProposicao'
        ])
        ->except('show')
        ->middlewareFor('index', 'can:viewAny,' . TipoProposicao::class)
        ->middlewareFor(['create', 'store'], 'can:create,' . TipoProposicao::class)
        ->middlewareFor(['edit', 'update'], 'can:update,tipoProposicao')
        ->middlewareFor('destroy', 'can:delete,tipoProposicao');
    // -----------------------------------------------------------------------------------------------

    Route::get('/proposicoes/arquivadas', [ProposicaoController::class, 'arquivadas'])
        ->middleware('can:viewArchived,' . Proposicao::class)
        ->name('proposicoes.arquivadas');

    Route::patch('/proposicoes/{proposicao}/restaurar', [ProposicaoController::class, 'restore'])
        ->withTrashed()
        ->middleware('can:restore,proposicao')
        ->name('proposicoes.restore');

    Route::patch('/proposicoes/{proposicao}/protocolar', [ProposicaoController::class, 'protocolar'])
        ->middleware('can:protocolar,proposicao')
        ->name('proposicoes.protocolar');

    Route::resource('proposicoes', ProposicaoController::class)
        ->parameters([
            'proposicoes' => 'proposicao'
        ])
        ->middlewareFor('index', 'can:viewAny,' . Proposicao::class)
        ->middlewareFor('show', 'can:view,proposicao')
        ->middlewareFor(['create', 'store'], 'can:create,' . Proposicao::class)
        ->middlewareFor(['edit', 'update'], 'can:update,proposicao')
        ->middlewareFor('destroy', 'can:delete,proposicao');
    // -----------------------------------------------------------------------------------------------
});
