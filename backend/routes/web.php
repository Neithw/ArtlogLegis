<?php

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

    Route::get('/camaras', function () {
        return 'Usuário autorizado a visualizar Câmaras.';
    })
        ->middleware('can:camaras.visualizar')
        ->name('camaras.index');
});
