<?php

use App\Http\Controllers\CamaraController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
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

    Route::resource('usuarios', UserController::class)
        ->parameters([
            'usuarios' => 'user',
        ])
        ->except('show')
        ->middlewareFor('index', 'can:usuarios:visualizar')
        ->middlewareFor(['create', 'store'], 'can:usuarios:criar')
        ->middlewareFor(['edit', 'update'], 'can:usuarios:editar')
        ->middlewareFor(['destroy'], 'can:usuarios:excluir');
});
