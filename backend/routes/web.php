<?php

use App\Http\Controllers\CamaraController;
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
        ->middlewareFor(
            ['index', 'show'],
            'can:camaras.visualizar'
        )
        ->middlewareFor(
            ['create', 'store'],
            'can:camaras.criar'
        )
        ->middlewareFor(
            ['edit', 'update'],
            'can:camaras.editar'
        )
        ->middlewareFor(
            'destroy',
            'can:camaras.excluir'
        );
});
