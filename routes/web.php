<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BibliController;

Route::get('/', function () {
    return view('welcome');
});

// Livros
Route::get('/livros/create', [BibliController::class, 'createLivro']);
// Formulário de novo livro
Route::post('/livros', [BibliController::class, 'storeLivro']);

// Autores
Route::get('/autores/create', [BibliController::class, 'createAutor']);
// Formulário de novo autor
Route::post('/autores', [BibliController::class, 'storeAutor']);

// Editoras
Route::get('/editoras/create', [BibliController::class, 'createEditora']);
Route::post('/editoras', [BibliController::class, 'storeEditora']);


// DELETE 
Route::delete('/livros/{id}', [BibliController::class, 'destroyLivro'])->name('livros.destroy');
Route::delete('/editoras/{id}', [BibliController::class, 'destroyEditora'])->name('editoras.destroy');
Route::delete('/autores/{id}', [BibliController::class, 'destroyAutor'])->name('autores.destroy');

//EDIT
Route::get('/livros/{id}/edit', [BibliController::class, 'editLivro'])->name('livros.edit');
Route::put('/livros/{id}', [BibliController::class, 'updateLivro'])->name('livros.update');

Route::get('/autores/{id}/edit', [BibliController::class, 'editAutor'])->name('autores.edit');
Route::put('/autores/{id}', [BibliController::class, 'updateAutor'])->name('autores.update');

Route::get('/editoras/{id}/edit', [BibliController::class, 'editEditora'])->name('editoras.edit');
Route::put('/editoras/{id}', [BibliController::class, 'updateEditora'])->name('editoras.update');


// Dashboard (protegido por login + verificação de email)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });


